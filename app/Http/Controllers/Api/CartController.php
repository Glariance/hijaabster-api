<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    private const SESSION_KEY = 'cart_items';

    private const FREE_SHIPPING_THRESHOLD = 5000;

    private const SHIPPING_COST = 200;

    private const TAX_RATE = 0.05;

    /**
     * Fetch CMS page data for the cart page (for debugging / use on page).
     */
    public function show(Request $request): JsonResponse
    {
        $slug = $request->query('slug', 'cart');

        $page = CmsPage::query()
            ->where('page_slug', $slug)
            ->with([
                'sections' => fn ($q) => $q->orderBy('section_sort_order'),
                'sections.fields' => fn ($q) => $q->orderBy('field_group')->orderBy('id'),
            ])
            ->first();

        if ($page === null && $slug !== 'cart') {
            $page = CmsPage::query()
                ->where('page_slug', 'cart')
                ->with([
                    'sections' => fn ($q) => $q->orderBy('section_sort_order'),
                    'sections.fields' => fn ($q) => $q->orderBy('field_group')->orderBy('id'),
                ])
                ->first();
        }

        $sections = $page ? $this->formatSections($page->sections) : [];

        return response()->json([
            'page' => [
                'id' => $page?->id,
                'title' => $page?->page_title,
                'slug' => $page?->page_slug ?? $slug,
                'meta' => [
                    'title' => $page?->page_meta_title,
                    'keywords' => $page?->page_meta_keyword,
                    'description' => $page?->page_meta_description,
                ],
                'sections' => $sections,
            ],
        ]);
    }

    /**
     * Get current cart from session (items + summary).
     */
    public function index(Request $request): JsonResponse
    {
        $items = $request->session()->get(self::SESSION_KEY, []);
        $items = is_array($items) ? $items : [];
        return response()->json($this->cartResponse($items));
    }

    /**
     * Add item to cart (session). Body: productId, slug, name, description, price, image?, quantity.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'productId' => 'required|integer',
            'slug' => 'required|string',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'quantity' => 'sometimes|integer|min:1',
        ]);
        $quantity = (int) Arr::get($validated, 'quantity', 1);
        $items = $request->session()->get(self::SESSION_KEY, []);
        $items = is_array($items) ? $items : [];
        $productId = (int) $validated['productId'];
        $found = false;
        foreach ($items as &$item) {
            if ((int) $item['productId'] === $productId) {
                $item['quantity'] = (int) $item['quantity'] + $quantity;
                $found = true;
                break;
            }
        }
        if (! $found) {
            $items[] = [
                'productId' => $productId,
                'slug' => $validated['slug'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'price' => (float) $validated['price'],
                'image' => $validated['image'] ?? null,
                'quantity' => $quantity,
            ];
        }
        $request->session()->put(self::SESSION_KEY, $items);
        return response()->json($this->cartResponse($items));
    }

    /**
     * Update quantity for a product. Body: quantity.
     */
    public function update(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:0']);
        $qty = (int) $validated['quantity'];
        $items = $request->session()->get(self::SESSION_KEY, []);
        $items = is_array($items) ? $items : [];
        $newItems = [];
        foreach ($items as $item) {
            if ((int) $item['productId'] === $productId) {
                if ($qty > 0) {
                    $item['quantity'] = $qty;
                    $newItems[] = $item;
                }
                continue;
            }
            $newItems[] = $item;
        }
        $request->session()->put(self::SESSION_KEY, $newItems);
        return response()->json($this->cartResponse($newItems));
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        $items = $request->session()->get(self::SESSION_KEY, []);
        $items = is_array($items) ? array_values(array_filter($items, fn ($i) => (int) $i['productId'] !== $productId)) : [];
        $request->session()->put(self::SESSION_KEY, $items);
        return response()->json($this->cartResponse($items));
    }

    /**
     * Compute cart summary from request body (for guest / client-side cart sync).
     * Body: { items: [{ productId, slug, name, description?, price, image?, quantity }] }
     */
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.productId' => 'required|integer',
            'items.*.slug' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        $items = $request->input('items', []);
        return response()->json($this->cartResponse($items));
    }

    private function cartResponse(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float) $item['price'] * (int) $item['quantity'];
        }
        $shipping = $subtotal >= self::FREE_SHIPPING_THRESHOLD ? 0 : self::SHIPPING_COST;
        $taxEstimate = round($subtotal * self::TAX_RATE);
        $total = $subtotal + $shipping + $taxEstimate;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'taxEstimate' => $taxEstimate,
            'total' => $total,
        ];
    }

    protected function formatSections(Collection $sections): array
    {
        return $sections
            ->map(static function ($section) {
                $base = [
                    'id' => $section->id,
                    'name' => $section->section_name,
                    'type' => $section->section_type,
                    'sort_order' => $section->section_sort_order,
                ];

                $fields = $section->fields->sortBy('id');

                if ($section->section_type === 'repeater') {
                    $base['items'] = $fields
                        ->groupBy(static fn ($field) => $field->field_group ?? 'default')
                        ->sortKeys()
                        ->map(static function (Collection $fieldGroup) {
                            return $fieldGroup
                                ->sortBy('id')
                                ->mapWithKeys(static function ($field) {
                                    $payload = [
                                        'type' => $field->field_type,
                                        'value' => $field->field_value,
                                    ];

                                    if ($field->field_type === 'image' && $field->field_value) {
                                        $payload['url'] = self::resolveImageUrl($field->field_value);
                                    }

                                    return [
                                        $field->field_name => $payload,
                                    ];
                                })
                                ->all();
                        })
                        ->values()
                        ->all();
                } else {
                    $base['fields'] = $fields
                        ->mapWithKeys(static function ($field) {
                            $payload = [
                                'type' => $field->field_type,
                                'value' => $field->field_value,
                            ];

                            if ($field->field_type === 'image' && $field->field_value) {
                                $payload['url'] = self::resolveImageUrl($field->field_value);
                            }

                            return [
                                $field->field_name => $payload,
                            ];
                        })
                        ->all();
                }

                return $base;
            })
            ->values()
            ->all();
    }

    protected static function resolveImageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return route('media.asset', ['path' => ltrim($path, '/')]);
        }

        return $path;
    }
}
