<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Brand::with('media')->orderBy('status', 'Desc')->orderBy('id', 'Desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    if ($row->media && $row->media->path) {
                        $imagePath = asset($row->media->path);
                        return '<img src="' . $imagePath . '" alt="' . $row->name . '" width="120" height="120" style="object-fit: cover; border-radius: 4px;">';
                    } else {
                        return defaultBadge('No Image', 100);
                    }
                })
                ->editColumn('status', function ($row) {
                    return defaultBadge(config('constants.status.' . $row->status), 25);
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.inventory.brand.show', $row->id);
                    $editUrl = route('admin.inventory.brand.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Brand Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Brand Details\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.inventory.brand.destroy', $row->id) . '`)" class="btn btn-light text-danger"><i class="bx bx-trash"></i></a>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }
        return view('admin.pages.inventory.brand.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.inventory.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:brands,name',
            'slug'   => 'required|string|max:255|unique:brands,slug',
            'status' => 'required|boolean',
            'description' => 'nullable|String',
            'file'   => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',
        ]);

        try {
            DB::beginTransaction();
            $brand = Brand::create($validated);
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $storedPath = $file->store('brands', 'public');
                $mime = $file->getMimeType();
                $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');
                $brand->media()->create([
                    'path' => "/storage/{$storedPath}",
                    'media_type' => $mediaType,
                    'is_featured' => true,
                ]);
            }
            DB::commit();
            return response()->json(['success' => "Brand created successfully."]);
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
        $brand = Brand::findOrFail($id);
        return view('admin.pages.inventory.brand.show', compact('brand'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.pages.inventory.brand.create', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug'   => 'required|string|max:255|unique:brands,slug,' . $brand->id,
            'status' => 'required|boolean',
            'description' => 'nullable|String',
            'file'   => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',
        ]);

        try {
            DB::beginTransaction();

            $brand->update($validated);

            // Handle single image replacement
            if ($request->hasFile('file')) {
                $existingMedia = $brand->media()->first();
                if ($existingMedia) {
                    $oldPath = public_path($existingMedia->path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $existingMedia->delete();
                }

                $file = $request->file('file');
                $storedPath = $file->store('brands', 'public');
                $mime = $file->getMimeType();
                $mediaType = str()->startsWith($mime, 'image') ? 'image' : (str()->startsWith($mime, 'video') ? 'video' : 'unknown');

                $brand->media()->create([
                    'path' => "/storage/{$storedPath}",
                    'media_type' => $mediaType,
                    'is_featured' => true,
                ]);
            }

            DB::commit();
            return response()->json(['success' => "Brand updated successfully."]);
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
        try {
            DB::beginTransaction();

            $brand = Brand::with('media')->findOrFail($id);

            // Delete associated media file if exists
            if ($brand->media) {
                $filePath = public_path($brand->media->path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $brand->media->delete();
            }

            // Delete the brand
            $brand->delete();

            DB::commit();
            return response()->json(['success' => 'Brand deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => 'Failed to delete brand: ' . $e->getMessage()], 500);
        }
    }
}
