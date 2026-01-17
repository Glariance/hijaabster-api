<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Product::with(['brand', 'category', 'coupon', 'mediaFeatured'])
                ->when(request('category_id'), function ($query, $categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->orderBy('status', 'desc')
                ->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if ($row->mediaFeatured && $row->mediaFeatured->path) {
                        $imagePath = asset($row->mediaFeatured->path);
                        return '<img src="' . $imagePath . '" alt="' . $row->name . '" width="120" height="120" style="object-fit: cover; border-radius: 4px;">';
                    } else {
                        return defaultBadge('No Image', 100);
                    }
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.inventory.product.show', $row->id);
                    $editUrl = route('admin.inventory.product.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Product Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    // $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Product Details\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $editBtn = '<a href="' . $editUrl . '" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.inventory.product.destroy', $row->id) . '`)" class="btn btn-light text-danger"><i class="bx bx-trash"></i></a>';
                    // $deleteBtn
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('status', function ($row) {
                    return defaultBadge(config('constants.product.status.' . $row->status));
                })
                ->editColumn('featured', function ($row) {
                    return defaultBadge(config('constants.product.featured.' . $row->featured));
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->editColumn('brand_id', function ($row) {
                    return $row->brand->name ?? "N/A";
                })
                ->editColumn('category_id', function ($row) {
                    return $row->category->name ?? "N/A";
                })
                ->addColumn('coupon_id', function ($row) {
                    return $row->coupon->name ?? "N/A";
                })
                ->rawColumns(['image', 'action', 'status', 'featured']) // Allow HTML in these columns
                ->make(true);
        }
        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('status', 1)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        return view('admin.pages.inventory.product.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('status', 1)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $coupons = \App\Models\Coupon::where('status', 1)->orderBy('name')->get();
        return view('admin.pages.inventory.product.create', compact('categories', 'brands', 'coupons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'created_by' => Auth::id(),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'has_discount' => 'nullable|in:0,1',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'coupon_id' => 'nullable|exists:coupons,id',
            'featured' => 'nullable|in:0,1',
            'new' => 'nullable|in:0,1',
            'top' => 'nullable|in:0,1',
            'status' => 'required|in:0,1',
            'created_by' => 'required|exists:users,id',
        ], [
            'files.required' => 'Please upload at least one product image.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please check the form.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $validated = $validator->validated();
            $status = $request->boolean('status');
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'base_price' => $validated['base_price'] ?? 0,
                'stock' => $validated['stock'] ?? 0,
                'has_variations' => 0,
                'category_id' => $validated['category_id'] ?? $this->resolveDefaultCategoryId(),
                'brand_id' => !empty($validated['brand_id']) ? $validated['brand_id'] : null,
                'has_discount' => $request->has('has_discount') ? 1 : 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'coupon_id' => !empty($validated['coupon_id']) ? $validated['coupon_id'] : null,
                'created_by' => $validated['created_by'],
                'featured' => $request->has('featured') ? 1 : 0,
                'new' => $request->has('new') ? 1 : 0,
                'top' => $request->has('top') ? 1 : 0,
                'status' => $status ? 1 : 0,
            ]);

            // Handle featured media selection
            $featuredMediaId = $request->input('featured_media_id');
            $newFeaturedIndex = $request->input('new_featured_index', 0);
            
            // First, unset all existing featured images
            if ($featuredMediaId) {
                $product->media()->update(['is_featured' => 0]);
            }
            
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $newMediaIds = [];
                foreach ($files as $index => $file) {
                    $storedPath = $file->store('products', 'public');
                    $mime = $file->getMimeType();
                    $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');
                    $newMedia = $product->media()->create([
                        'path' => "/storage/{$storedPath}",
                        'media_type' => $mediaType,
                        'is_featured' => 0, // Will be set below if needed
                    ]);
                    $newMediaIds[] = $newMedia->id;
                }
                
                // If no existing featured selected, make the selected new one featured
                if (!$featuredMediaId && isset($newMediaIds[$newFeaturedIndex])) {
                    $product->media()->where('id', $newMediaIds[$newFeaturedIndex])->update(['is_featured' => 1]);
                }
            }
            
            // Set the selected existing media as featured
            if ($featuredMediaId) {
                $product->media()->where('id', $featuredMediaId)->update(['is_featured' => 1]);
            }

            DB::commit();

            return response()->json([
                'success' => 'Product created successfully.',
                'product' => $product->load('media'),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['brand', 'category.parent', 'media'])->findOrFail($id);
        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('status', 1)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        return view('admin.pages.inventory.product.show', compact('product', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with(['media', 'mediaFeatured'])->findOrFail($id);

        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('status', 1)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $brands = Brand::where('status', 1)->orderBy('name')->get();
        $coupons = \App\Models\Coupon::where('status', 1)->orderBy('name')->get();
        return view('admin.pages.inventory.product.create', compact('product', 'categories', 'brands', 'coupons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'has_discount' => 'nullable|in:0,1',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'coupon_id' => 'nullable|exists:coupons,id',
            'featured' => 'nullable|in:0,1',
            'new' => 'nullable|in:0,1',
            'top' => 'nullable|in:0,1',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please check the form.'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $validated = $validator->validated();
            $status = $request->boolean('status');
            
            // Prepare brand_id - convert empty string to null
            $brandId = $request->input('brand_id');
            $brandId = ($brandId === '' || $brandId === null || $brandId === '0') ? null : (int)$brandId;
            
            // Prepare coupon_id - convert empty string to null
            $couponId = $request->input('coupon_id');
            $couponId = ($couponId === '' || $couponId === null || $couponId === '0') ? null : (int)$couponId;
            
            $product->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'base_price' => $validated['base_price'] ?? 0,
                'stock' => $validated['stock'] ?? 0,
                'category_id' => $validated['category_id'],
                'brand_id' => $brandId,
                'has_discount' => $request->has('has_discount') ? 1 : 0,
                'discount_type' => $validated['discount_type'] ?? null,
                'discount_value' => $validated['discount_value'] ?? 0,
                'coupon_id' => $couponId,
                'featured' => $request->has('featured') ? 1 : 0,
                'new' => $request->has('new') ? 1 : 0,
                'top' => $request->has('top') ? 1 : 0,
                'status' => $status ? 1 : 0,
            ]);

            // Handle featured media selection
            $featuredMediaId = $request->input('featured_media_id');
            $newFeaturedIndex = $request->input('new_featured_index', 0);
            
            // First, unset all existing featured images
            $product->media()->update(['is_featured' => 0]);
            
            // Add new files if any
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $newMediaIds = [];
                foreach ($files as $index => $file) {
                    $storedPath = $file->store('products', 'public');
                    $mime = $file->getMimeType();
                    $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');
                    $newMedia = $product->media()->create([
                        'path' => "/storage/{$storedPath}",
                        'media_type' => $mediaType,
                        'is_featured' => 0, // Will be set below if needed
                    ]);
                    $newMediaIds[] = $newMedia->id;
                }
                
                // If no existing featured selected, make the selected new one featured
                if (!$featuredMediaId && isset($newMediaIds[$newFeaturedIndex])) {
                    $product->media()->where('id', $newMediaIds[$newFeaturedIndex])->update(['is_featured' => 1]);
                }
            }
            
            // Set the selected existing media as featured
            if ($featuredMediaId) {
                $product->media()->where('id', $featuredMediaId)->update(['is_featured' => 1]);
            } else if ($request->hasFile('files') && !$featuredMediaId) {
                // If no existing featured selected and new files uploaded, make the selected new one featured
                $newMedia = $product->media()->whereNull('deleted_at')->orderBy('id', 'desc')->skip($product->media()->count() - count($files))->take(count($files))->get();
                if (isset($newMedia[$newFeaturedIndex])) {
                    $newMedia[$newFeaturedIndex]->update(['is_featured' => 1]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => 'Product updated successfully.',
                'product' => $product->load('media'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Check if this is a media deletion request by checking if ID exists in media table
        $media = \App\Models\Media::find($id);
        if ($media) {
            // Check if it belongs to a product
            $product = Product::find($media->mediaable_id);
            if ($product && $media->mediaable_type === Product::class) {
                // Delete individual media permanently
                DB::beginTransaction();
                try {
                    if ($media->path) {
                        $path = ltrim(str_replace('storage/', '', $media->path), '/');
                        if ($path) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                    // Permanently delete from database (not soft delete)
                    $media->forceDelete();
                    
                    DB::commit();
                    return response()->json(['success' => 'Image deleted successfully.']);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Failed to delete image.',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }

        // Delete entire product
        $product = Product::with('media')->findOrFail($id);

        DB::beginTransaction();
        try {
            $product->media()->each(function ($media) {
                if ($media->path) {
                    $path = ltrim(str_replace('storage/', '', $media->path), '/');
                    if ($path) {
                        Storage::disk('public')->delete($path);
                    }
                }
                // Permanently delete from database (not soft delete)
                $media->forceDelete();
            });

            $product->delete();

            DB::commit();
            return response()->json(['success' => 'Product deleted successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'product';
        $slug = $baseSlug;
        $counter = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    private function resolveDefaultCategoryId(): int
    {
        $categoryId = Category::where('status', 1)->value('id');

        if ($categoryId) {
            return $categoryId;
        }

        return Category::firstOrCreate(
            ['slug' => 'default-category'],
            [
                'name' => 'Default Category',
                'status' => 1,
            ]
        )->id;
    }

    private function resolveDefaultBrandId(): int
    {
        $brandId = Brand::where('status', 1)->value('id');

        if ($brandId) {
            return $brandId;
        }

        return Brand::firstOrCreate(
            ['slug' => 'default-brand'],
            [
                'name' => 'Default Brand',
                'status' => 1,
            ]
        )->id;
    }

    private function storeOrReplaceProductImage(Product $product, ?UploadedFile $file = null): void
    {
        if (!$file) {
            return;
        }

        $product->media()->each(function ($media) {
            if ($media->path) {
                Storage::disk('public')->delete($media->path);
            }
            $media->delete();
        });

        $storedPath = $file->store('products', 'public');
        $mime = $file->getMimeType();

        $product->media()->create([
            'path' => $storedPath,
            'media_type' => Str::startsWith($mime, 'image') ? 'image' : 'video',
            'is_featured' => 1,
        ]);
    }
}
