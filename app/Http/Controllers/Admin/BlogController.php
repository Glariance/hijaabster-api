<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Blog::select(['id', 'title', 'slug', 'status', 'created_at'])->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.blogs.edit', $row->id);
                    $showUrl = route('admin.blogs.show', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Show Blog Details\', \'\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Blog\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteBlog(' . $row->id . ', `' . route('admin.blogs.destroy', $row->id) . '`)" class="btn btn-light"><i class="bx bx-trash"></i></a>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->editColumn('status', function ($row) {
                    return strtoupper($row->status);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.pages.blog.index');
    }

    public function create()
    {
        $tags = Tag::all();
        return view('admin.pages.blog.create', compact('tags'));
    }

    public function store(Request $request)
    {
        // dd($request->file('file'));
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title',
            'slug' => 'required|string|max:255|unique:blogs,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'description' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'file' => 'nullable|array',
            'file.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240' // 10MB limit
        ]);
        try {
            DB::beginTransaction();

            $blog = Blog::create($validated);

            // Attach tags if provided
            if ($request->has('tags')) {
                $blog->tags()->attach($request->tags);
            }
            // Handle uploaded files (images or videos)
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $storedPath = $file->store('blogs', 'public');

                    $mime = $file->getMimeType();
                    $mediaType = Str::startsWith($mime, 'image') ? 'image' : (Str::startsWith($mime, 'video') ? 'video' : 'unknown');
                    $blog->media()->create([
                        'path' => "/storage/" . $storedPath,
                        'media_type' => $mediaType,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Blog created successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function show(Blog $blog)
    {
        return view('admin.pages.blog.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $tags = Tag::all();
        return view('admin.pages.blog.create', compact('blog', 'tags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:blogs,title,' . $blog->id,
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $blog->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'description' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'file' => 'nullable|array',
            'file.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $blog->update($validated);

            // Sync tags
            if ($request->has('tags')) {
                $blog->tags()->sync($request->tags);
            } else {
                $blog->tags()->detach();
            }

            // Handle uploaded files
            if ($request->hasFile('file')) {
                // Optional: delete old media if you want to replace them
                // $blog->media()->delete();
                foreach ($request->file('file') as $file) {
                    $storedPath = $file->store('blogs', 'public');

                    $mime = $file->getMimeType();
                    $mediaType = Str::startsWith($mime, 'image') ? 'image' : (Str::startsWith($mime, 'video') ? 'video' : 'unknown');

                    $blog->media()->create([
                        'path' => "/storage/" . $storedPath,
                        'media_type' => $mediaType,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success' => 'Blog updated successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Blog $blog)
    {
        // Soft delete all related comments (including nested ones)
        foreach ($blog->comments as $comment) {
            Comment::where('parent_id', $comment->id)->delete();
            $comment->delete();
        }

        // Delete pivot table blog_tag relation (if you're using a many-to-many)
        $blog->tags()->detach(); // not soft delete, just removes relation

        // Soft delete blog media (if media has soft deletes)
        foreach ($blog->media as $media) {
            $media->delete(); // soft delete
        }

        // Finally, soft delete the blog itself
        $blog->delete();

        return response()->json(['success' => 'Blog and related data deleted successfully.']);
    }

    public function destroyMedia(Media $media)
    {
        $media->delete();
        return response()->json(['success' => "Blog Media deleted successfully."]);
    }

    public function destroyComment(Comment $comment)
    {
        Comment::where('parent_id', $comment->id)->delete();
        $comment->delete();
        return response()->json(['success' => "Blog Comment deleted successfully."]);
    }

    public function updateCommentStatus(Request $request, Comment $comment)
    {
        // dd($request->all());
        $comment->update($request->only('status'));
        return response()->json(['success' => "Comment Status Updated successfully."]);
    }
}
