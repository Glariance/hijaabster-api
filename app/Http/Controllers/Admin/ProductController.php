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
            $data = Product::with(['brand', 'category'])
                ->when(request('category_id'), function ($query, $categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->orderBy('status', 'desc')
                ->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
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
                ->addColumn('image', function ($row) {
                    $imagePath = asset("storage/" . $row->mediaFeatured?->path);
                    return "<img src='" . $imagePath . "' alt='' width='150'/>";
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
                ->rawColumns(['action', 'status', 'featured', 'image']) // Allow HTML in these columns
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
        return view('admin.pages.inventory.product.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'created_by' => Auth::id(),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amazon_link' => 'nullable|url|max:2048',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'category_id' => 'required|exists:categories,id',
            'created_by' => 'required|exists:users,id',
        ], [
            'image.required' => 'Please upload at least one product image.',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $this->generateUniqueSlug($validated['name']),
                'description' => $validated['description'] ?? null,
                'amazon_link' => $validated['amazon_link'] ?? null,
                'base_price' => 0,
                'stock' => 0,
                'has_variations' => 0,
                'category_id' => $validated['category_id'] ?? $this->resolveDefaultCategoryId(),
                'brand_id' => $this->resolveDefaultBrandId(),
                'has_discount' => 0,
                'discount_type' => null,
                'discount_value' => 0,
                'created_by' => $validated['created_by'],
                'featured' => 0,
                'new' => 0,
                'top' => 0,
                'status' => 1,
            ]);

            $this->storeOrReplaceProductImage($product, $request->file('image'));

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
        return view('admin.pages.inventory.product.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amazon_link' => 'nullable|url|max:2048',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:20480',
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|in:0,1',
        ]);

        DB::beginTransaction();

        try {
            $status = $request->boolean('status');
            $product->update([
                'name' => $validated['name'],
                'slug' => $this->generateUniqueSlug($validated['name'], $product->id),
                'description' => $validated['description'] ?? null,
                'amazon_link' => $validated['amazon_link'] ?? null,
                'category_id' => $validated['category_id'],
                'status' => $status ? 1 : 0,
            ]);

            if ($request->hasFile('image')) {
                $this->storeOrReplaceProductImage($product, $request->file('image'));
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
        $product = Product::with('media')->findOrFail($id);

        DB::beginTransaction();
        try {
            $product->media()->each(function ($media) {
                if ($media->path) {
                    Storage::disk('public')->delete($media->path);
                }
                $media->delete();
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
