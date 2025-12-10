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
            $data = Category::orderBy('status', 'desc')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.inventory.category.show', $row->id);
                    $editUrl = route('admin.inventory.category.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Category Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Category Details\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.inventory.category.destroy', $row->id) . '`)" class="btn btn-light text-danger"><i class="bx bx-trash"></i></a>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('parent_id', function ($row) {
                    return $row->parent->name ?? "";
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['action'])
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
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        try {
            DB::beginTransaction();
            $category = Category::create($validated);
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $storedPath = $file->store('categories', 'public');
                $mime = $file->getMimeType();
                $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');
                $category->media()->create([
                    'path' => "/storage/{$storedPath}",
                    'media_type' => $mediaType,
                    'is_featured' => true,
                ]);
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
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);
        try {
            DB::beginTransaction();
            $category->update($validated);
            if ($request->hasFile('file')) {
                $existingMedia = $category->media()->first();
                if ($existingMedia) {
                    $oldPath = public_path($existingMedia->path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $existingMedia->delete();
                }
                $file = $request->file('file');
                $storedPath = $file->store('categories', 'public');
                $mime = $file->getMimeType();
                $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');

                $category->media()->create([
                    'path' => "/storage/{$storedPath}",
                    'media_type' => $mediaType,
                    'is_featured' => true,
                ]);
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
                $media->delete();
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
