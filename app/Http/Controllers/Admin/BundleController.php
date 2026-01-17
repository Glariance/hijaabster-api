<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class BundleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Bundle::with('products')->orderBy('status', 'Desc')->orderBy('id', 'Desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('products_count', function ($row) {
                    return $row->products->count();
                })
                ->addColumn('total_price', function ($row) {
                    return 'PKR ' . number_format($row->total_price ?? 0, 2);
                })
                ->addColumn('bundle_price', function ($row) {
                    return 'PKR ' . number_format($row->bundle_price ?? 0, 2);
                })
                ->editColumn('status', function ($row) {
                    return defaultBadge(config('constants.status.' . $row->status), 25);
                })
                ->editColumn('discount_type', function ($row) {
                    return $row->discount_type === 'percentage' 
                        ? $row->discount_value . '%' 
                        : 'PKR ' . number_format($row->discount_value, 2);
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.bundle.show', $row->id);
                    $editUrl = route('admin.bundle.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Bundle Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.bundle.destroy', $row->id) . '`)" class="btn btn-light text-danger"><i class="bx bx-trash"></i></a>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.pages.bundle.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::where('status', 1)
            ->with(['category', 'mediaFeatured'])
            ->orderBy('name')
            ->get();
        return view('admin.pages.bundle.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bundles,slug',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'nullable|numeric|min:0',
            'products.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please check the form.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();
            $data['created_by'] = Auth::id();

            // Generate slug if not provided or empty
            if (empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
                // Ensure uniqueness
                $originalSlug = $data['slug'];
                $counter = 1;
                while (Bundle::where('slug', $data['slug'])->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Calculate total price
            $totalPrice = 0;
            foreach ($data['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $price = $productData['price'] ?? $product->base_price;
                $totalPrice += $price * $productData['quantity'];
            }
            $data['total_price'] = $totalPrice;

            // Calculate bundle price
            if ($data['discount_type'] === 'percentage') {
                $discount = ($totalPrice * $data['discount_value']) / 100;
                $data['bundle_price'] = $totalPrice - $discount;
            } else {
                $data['bundle_price'] = max(0, $totalPrice - $data['discount_value']);
            }

            // Create bundle
            $bundle = Bundle::create($data);

            // Attach products
            $productsToAttach = [];
            foreach ($data['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $productsToAttach[$productData['product_id']] = [
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'] ?? $product->base_price,
                    'sort_order' => $productData['sort_order'] ?? 0,
                ];
            }
            $bundle->products()->attach($productsToAttach);

            DB::commit();
            return response()->json(['success' => "Bundle created successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => 'An error occurred while creating the bundle.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bundle = Bundle::with(['products', 'creator'])->findOrFail($id);
        return view('admin.pages.bundle.show', compact('bundle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bundle = Bundle::with('products')->findOrFail($id);
        $products = Product::where('status', 1)
            ->with(['category', 'mediaFeatured'])
            ->orderBy('name')
            ->get();
        return view('admin.pages.bundle.create', compact('bundle', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bundle $bundle)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bundles,slug,' . $bundle->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'nullable|numeric|min:0',
            'products.*.sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please check the form.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();

            // Generate slug if not provided or empty
            if (empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
                // Ensure uniqueness (excluding current bundle)
                $originalSlug = $data['slug'];
                $counter = 1;
                while (Bundle::where('slug', $data['slug'])->where('id', '!=', $bundle->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Calculate total price
            $totalPrice = 0;
            foreach ($data['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $price = $productData['price'] ?? $product->base_price;
                $totalPrice += $price * $productData['quantity'];
            }
            $data['total_price'] = $totalPrice;

            // Calculate bundle price
            if ($data['discount_type'] === 'percentage') {
                $discount = ($totalPrice * $data['discount_value']) / 100;
                $data['bundle_price'] = $totalPrice - $discount;
            } else {
                $data['bundle_price'] = max(0, $totalPrice - $data['discount_value']);
            }

            // Update bundle
            $bundle->update($data);

            // Sync products
            $productsToSync = [];
            foreach ($data['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $productsToSync[$productData['product_id']] = [
                    'quantity' => $productData['quantity'],
                    'price' => $productData['price'] ?? $product->base_price,
                    'sort_order' => $productData['sort_order'] ?? 0,
                ];
            }
            $bundle->products()->sync($productsToSync);

            DB::commit();
            return response()->json(['success' => "Bundle updated successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => 'An error occurred while updating the bundle.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $bundle = Bundle::findOrFail($id);
            $bundle->delete();
            DB::commit();
            return response()->json(['success' => 'Bundle deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => 'Failed to delete bundle: ' . $e->getMessage()], 500);
        }
    }
}
