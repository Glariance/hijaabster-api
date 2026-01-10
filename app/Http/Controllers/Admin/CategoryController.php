<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Category::with(['mediaFeatured', 'parent'])->orderBy('status', 'desc')->orderBy('id', 'desc');
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
                    $showUrl = route('admin.inventory.category.show', $row->id);
                    $editUrl = route('admin.inventory.category.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Category Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Category Details\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.inventory.category.destroy', $row->id) . '`)" class="btn btn-light text-danger"><i class="bx bx-trash"></i></a>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('parent_id', function ($row) {
                    if ($row->parent) {
                        return defaultBadge($row->parent->name, 25);
                    } else {
                        return defaultBadge('No Parent', 100);
                    }
                })
                ->editColumn('status', function ($row) {
                    return defaultBadge(config('constants.status.' . $row->status), 100);
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['image', 'parent_id', 'status', 'action'])
                ->make(true);
        }
        return view('admin.pages.inventory.category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.pages.inventory.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id'    => 'nullable|exists:categories,id',
            'name'         => 'required|string|max:255|unique:categories,name',
            'slug'         => 'required|string|max:255|unique:categories,slug',
            'description'  => 'nullable|string',
            'status'       => 'required|boolean',
            'files'        => 'nullable|array',
            'files.*'      => 'file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            DB::beginTransaction();
            $category = Category::create($validated);
            
            // Handle featured media selection
            $featuredMediaId = $request->input('featured_media_id');
            $newFeaturedIndex = $request->input('new_featured_index', 0);
            
            // First, unset all existing featured images
            if ($featuredMediaId) {
                $category->media()->update(['is_featured' => 0]);
            }
            
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                foreach ($files as $index => $file) {
                    $storedPath = $file->store('categories', 'public');
                    $mime = $file->getMimeType();
                    $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');
                    $category->media()->create([
                        'path' => "/storage/{$storedPath}",
                        'media_type' => $mediaType,
                        'is_featured' => !$featuredMediaId && $index == $newFeaturedIndex, // Featured if no existing featured selected and this is the selected new one
                    ]);
                }
            }
            
            // Set the selected existing media as featured
            if ($featuredMediaId) {
                $category->media()->where('id', $featuredMediaId)->update(['is_featured' => 1]);
            }
            DB::commit();
            return response()->json(['success' => "Category created successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.pages.inventory.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::where('status', 1)->get();
        $category = Category::findOrFail($id);
        return view('admin.pages.inventory.category.create', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'parent_id'   => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug'        => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
            'files'       => 'nullable|array',
            'files.*'     => 'file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);
        try {
            DB::beginTransaction();
            $category->update($validated);
            
            // Handle featured media selection
            $featuredMediaId = $request->input('featured_media_id');
            $newFeaturedIndex = $request->input('new_featured_index', 0);
            
            // First, unset all existing featured images
            $category->media()->update(['is_featured' => 0]);
            
            // Add new files if any
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $newMediaIds = [];
                foreach ($files as $index => $file) {
                    $storedPath = $file->store('categories', 'public');
                    $mime = $file->getMimeType();
                    $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');
                    $newMedia = $category->media()->create([
                        'path' => "/storage/{$storedPath}",
                        'media_type' => $mediaType,
                        'is_featured' => 0, // Will be set below if needed
                    ]);
                    $newMediaIds[] = $newMedia->id;
                }
                
                // If no existing featured selected, make the selected new one featured
                if (!$featuredMediaId && isset($newMediaIds[$newFeaturedIndex])) {
                    $category->media()->where('id', $newMediaIds[$newFeaturedIndex])->update(['is_featured' => 1]);
                }
            }
            
            // Set the selected existing media as featured
            if ($featuredMediaId) {
                $category->media()->where('id', $featuredMediaId)->update(['is_featured' => 1]);
            }
            DB::commit();
            return response()->json(['success' => "Category updated successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()]);
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
            // Check if it belongs to a category
            $category = Category::find($media->mediaable_id);
            if ($category && $media->mediaable_type === Category::class) {
                // Delete individual media permanently
                DB::beginTransaction();
                try {
                    if ($media->path) {
                        $path = ltrim(str_replace('storage/', '', $media->path), '/');
                        if ($path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                        }
                    }
                    // Permanently delete from database (not soft delete)
                    $isFeatured = $media->is_featured;
                    $media->forceDelete();
                    
                    // If deleted media was featured, make the first remaining media featured
                    if ($isFeatured) {
                        $firstMedia = $category->media()->first();
                        if ($firstMedia) {
                            $firstMedia->update(['is_featured' => 1]);
                        }
                    }
                    
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

        // Delete entire category
        $category = Category::with('media')->findOrFail($id);

        DB::beginTransaction();
        try {
            $category->media()->each(function ($media) {
                if ($media->path) {
                    $path = ltrim(str_replace('storage/', '', $media->path), '/');
                    if ($path) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                    }
                }
                // Permanently delete from database (not soft delete)
                $media->forceDelete();
            });

            $category->delete();

            DB::commit();
            return response()->json(['success' => 'Category deleted successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
