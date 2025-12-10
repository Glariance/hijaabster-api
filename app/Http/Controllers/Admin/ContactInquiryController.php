<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactInquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactInquiry::select(['id', 'name', 'email', 'subject', 'is_read', 'created_at'])->orderBy('is_read', 'ASC')->orderBy('id', 'DESC');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $sendBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Contact Inquiry Response\', \'Send\', `' . route('admin.newsletter-management.send-mail-view', ['email' => $row->email, 'type' => 'contact', 'subject' => $row->subject]) . '`)" class="btn btn-light"><i class="lni lni-envelope"></i></a>';
                    $viewBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Contact Inquiry Details\', \'\', `' . route('admin.contact-inquiry.show', $row->id) . '`)" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteInquiry(' . $row->id . ', `' . route('admin.contact-inquiry.destroy', $row->id) . '`)" class="btn btn-light"><i class="bx bx-trash"></i></a>';
                    return $sendBtn . ' ' . $viewBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->editColumn('is_read', function ($row) {
                    return $row->is_read ? defaultBadge(strtoupper("Read"), 100) : defaultBadge(strtoupper("UnRead"), 100);
                })
                ->rawColumns(['action', 'is_read'])
                ->make(true);
        }
        return view('admin.pages.contactinquiry.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactInquiry $contactInquiry)
    {
        // dd($contactInquiry);
        $contactInquiry->update(['is_read' => 1]);
        return view('admin.pages.contactinquiry.show', compact('contactInquiry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactInquiry $contactInquiry)
    {
        if (!$contactInquiry) {
            return response()->json(['message' => "Inquiry Not Found."], 404);
        }
        $contactInquiry->delete();
        return response()->json(['success' => 'Contact inquiry moved to trash!']);
    }
}
