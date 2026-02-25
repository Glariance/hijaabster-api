<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CheckoutWelcomeMail;
use App\Mail\OrderInvoiceAdminMail;
use App\Mail\OrderInvoiceCustomerMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Build absolute image URL for order item using /storage/ path (public/storage).
     */
    private static function orderItemImageUrl(string $path): string
    {
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        $url = url('storage/' . $path);
        if (! str_starts_with($url, 'http')) {
            $url = rtrim(config('app.url', 'http://localhost:8000'), '/') . '/' . ltrim($url, '/');
        }
        return $url;
    }

    /**
     * List orders for the authenticated user (by user_id or customer_email).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->userFromRequest($request);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = min((int) $request->input('per_page', 50), 100);
        $paginator = Order::with('items')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $orders = $paginator->getCollection()->map(fn (Order $o) => [
            'id' => $o->id,
            'tracking_number' => $o->tracking_number,
            'customer_name' => $o->customer_name,
            'customer_email' => $o->customer_email,
            'total' => (float) $o->total,
            'status' => $o->status,
            'created_at' => $o->created_at->toIso8601String(),
            'items_count' => $o->items->count(),
        ]);

        return response()->json([
            'data' => $orders->values()->all(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    /**
     * Get a single order by ID (must belong to the authenticated user).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->userFromRequest($request);
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $order = Order::with(['items.product.mediaFeatured'])
            ->where('id', $id)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $shipping = $order->shipping_address;
        $addr = is_array($shipping) ? $shipping : [];

        return response()->json([
            'id' => $order->id,
            'tracking_number' => $order->tracking_number,
            'status' => $order->status,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'shipping_cost' => (float) $order->shipping_cost,
            'tax_estimate' => (float) $order->tax_estimate,
            'total' => (float) $order->total,
            'notes' => $order->notes,
            'created_at' => $order->created_at->toIso8601String(),
            'shipping_address' => $addr,
            'items' => $order->items->map(function ($i) {
                $path = $i->product && $i->product->mediaFeatured && $i->product->mediaFeatured->path
                    ? $i->product->mediaFeatured->path
                    : null;
                $imageUrl = $path ? self::orderItemImageUrl($path) : null;
                return [
                    'product_id' => $i->product_id,
                    'name' => $i->name,
                    'slug' => $i->slug,
                    'price' => (float) $i->price,
                    'quantity' => (int) $i->quantity,
                    'line_total' => (float) $i->price * (int) $i->quantity,
                    'image_url' => $imageUrl,
                ];
            }),
        ]);
    }

    /**
     * Place order and store in database.
     * Body: items[], shippingOptionId?, shippingAddress?, customer (name, email, phone?), notes?,
     * create_account? (bool), payment_method? (cash_on_delivery|online).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.productId' => 'required|integer',
            'items.*.slug' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'shippingOptionId' => 'nullable|string',
            'shippingAddress' => 'required|array',
            'shippingAddress.addressLine1' => 'required|string|max:255',
            'shippingAddress.addressLine2' => 'nullable|string|max:255',
            'shippingAddress.city' => 'required|string|max:255',
            'shippingAddress.state' => 'required|string|max:255',
            'shippingAddress.postalCode' => 'required|string|max:64',
            'shippingAddress.country' => 'required|string|max:255',
            'customer' => 'required|array',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email',
            'customer.phone' => 'nullable|string|max:64',
            'notes' => 'nullable|string|max:2000',
            'create_account' => 'nullable|boolean',
            'payment_method' => 'nullable|string|in:cash_on_delivery,online',
        ], [
            'customer.name.required' => 'First name field is required.',
            'customer.email.required' => 'Email field is required.',
            'customer.email.email' => 'Please enter a valid email address.',
            'shippingAddress.required' => 'Shipping address is required.',
            'shippingAddress.addressLine1.required' => 'Street address field is required.',
            'shippingAddress.city.required' => 'City field is required.',
            'shippingAddress.state.required' => 'State / Province field is required.',
            'shippingAddress.postalCode.required' => 'Postal code field is required.',
            'shippingAddress.country.required' => 'Country / Region field is required.',
        ]);

        $items = $request->input('items', []);
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }
        $shippingOptionId = Arr::get($validated, 'shippingOptionId', 'express');
        $shippingCost = $shippingOptionId === 'white-glove' ? 45 : 0;
        $taxRate = 0.05;
        $taxEstimate = round($subtotal * $taxRate);
        $total = $subtotal + $shippingCost + $taxEstimate;
        $customer = $validated['customer'];
        $createAccount = (bool) Arr::get($validated, 'create_account', false);
        $paymentMethod = Arr::get($validated, 'payment_method', 'cash_on_delivery');

        $order = null;
        $userCreated = null;
        $plainPassword = null;

        $authUser = $this->userFromRequest($request);

        DB::transaction(function () use ($request, $items, $subtotal, $shippingCost, $taxEstimate, $total, $customer, $shippingOptionId, $createAccount, $paymentMethod, $authUser, &$order, &$userCreated, &$plainPassword) {
            $trackingNumber = $this->generateTrackingNumber();

            $userId = $authUser?->id;
            if ($userId === null && $createAccount && ! empty($customer['email'])) {
                $existing = User::where('email', $customer['email'])->first();
                if ($existing) {
                    $userId = $existing->id;
                } else {
                    $plainPassword = Str::random(10);
                    $userCreated = User::create([
                        'name' => $customer['name'] ?? 'Customer',
                        'email' => $customer['email'],
                        'password' => Hash::make($plainPassword),
                        'role_id' => config('constants.USER', 1),
                    ]);
                    $userId = $userCreated->id;
                }
            }
            if ($userId === null && ! empty($customer['email'])) {
                $existingByEmail = User::where('email', $customer['email'])->value('id');
                if ($existingByEmail) {
                    $userId = $existingByEmail;
                }
            }

            $order = Order::create([
                'user_id' => $userId,
                'tracking_number' => $trackingNumber,
                'customer_name' => $customer['name'],
                'customer_email' => $customer['email'],
                'customer_phone' => $customer['phone'] ?? null,
                'shipping_option_id' => $shippingOptionId,
                'shipping_cost' => $shippingCost,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'tax_estimate' => $taxEstimate,
                'total' => $total,
                'shipping_address' => $request->input('shippingAddress'),
                'notes' => $request->input('notes'),
                'status' => 'pending',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => (int) $item['productId'],
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'price' => (float) $item['price'],
                    'quantity' => (int) $item['quantity'],
                ]);
            }
        });

        $order->load('items');

        try {
            Mail::to($order->customer_email)->send(new OrderInvoiceCustomerMail($order));
        } catch (\Throwable $e) {
            report($e);
        }

        $adminEmail = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new OrderInvoiceAdminMail($order));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($userCreated && $plainPassword !== null) {
            try {
                Mail::to($userCreated->email)->send(new CheckoutWelcomeMail($userCreated, $plainPassword));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order received successfully.',
            'order' => [
                'id' => $order->id,
                'tracking_number' => $order->tracking_number,
                'subtotal' => (float) $order->subtotal,
                'shipping' => (float) $order->shipping_cost,
                'taxEstimate' => (float) $order->tax_estimate,
                'total' => (float) $order->total,
                'payment_method' => $order->payment_method,
                'customer_email' => $order->customer_email,
            ],
        ], 201);
    }

    /**
     * Track order by tracking number. GET ?tracking=TRK-XXXX-XXXX
     */
    public function track(Request $request): JsonResponse
    {
        $tracking = $request->query('tracking');
        if (! $tracking || ! is_string($tracking)) {
            return response()->json(['message' => 'Tracking number is required.'], 422);
        }

        $order = Order::with('items')->where('tracking_number', $tracking)->first();
        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        return response()->json([
            'order' => [
                'tracking_number' => $order->tracking_number,
                'status' => $order->status,
                'created_at' => $order->created_at->toIso8601String(),
                'customer_name' => $order->customer_name,
                'total' => (float) $order->total,
                'items_count' => $order->items->count(),
            ],
        ]);
    }

    private function generateTrackingNumber(): string
    {
        do {
            $number = 'TRK-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (Order::where('tracking_number', $number)->exists());

        return $number;
    }

    private function userFromRequest(Request $request): ?User
    {
        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = substr($authHeader, 7);
        if ($token === '') {
            return null;
        }
        return User::where('remember_token', $token)->first();
    }
}
