<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Tag::select(['id', 'name', 'created_at'])->orderBy('id', 'DESC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.tags.edit', $row->id);

                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Tag\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.tags.destroy', $row->id) . '`)" class="btn btn-light"><i class="bx bx-trash"></i></a>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pages.tag.index');
    }

    public function create()
    {
        return view('admin.pages.tag.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name',
        ]);
        try {
            Tag::create(['name' => $request->name]);
            return response()->json(['success' => "Tag created successfully."]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function show(string $id)
    {
        //
    }
    public function edit(Tag $tag)
    {
        return view('admin.pages.tag.create', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|unique:tags,name,' . $tag->id,
        ]);
        try {
            $tag->update(['name' => $request->name]);
            return response()->json(['success' => "Tag updated successfully."]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(['success' => "Tag deleted successfully."]);
    }
}
