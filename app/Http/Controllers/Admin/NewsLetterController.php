<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class NewsLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Newsletter::select(['id', 'email', 'created_at', 'is_subscribed'])->orderBy('is_subscribed', 'DESC')->orderBy('id', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($newsletter) {
                    $editUrl = route('admin.newsletter-management.edit', $newsletter->id);
                    $deleteUrl = route('admin.newsletter-management.destroy', $newsletter->id);

                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Update Existing Email\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';

                    $deleteBtn = '<a href="javascript:;" class="btn btn-light" onclick="deleteNewsLetter(' . $newsletter->id . ', \'' . $deleteUrl . '\')"><i class="bx bx-trash"></i></a>';

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($newsletter) {
                    return $newsletter->created_at->format('d M Y');
                })
                ->editColumn('is_subscribed', function ($newsletter) {
                    return $newsletter->is_subscribed ? defaultBadge(strtoupper("Subscribed"), 100) : defaultBadge(strtoupper("Unsubscribed"), 100);
                })
                ->rawColumns(['action', 'is_subscribed']) // Allow HTML in these columns
                ->make(true);
        }

        return view('admin.pages.newsletter.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.newsletter.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:newsletters,email',
            ], [
                'email.unique' => "The entered email already exists in our record"
            ]);
            Newsletter::create(['email' => $request->email]);
            return response()->json([
                'success' => "Newsletter added successfully!"
            ]);
            // return redirect()->back()->with('success', 'Newsletter added successfully!');
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Public subscription endpoint (e.g., footer form).
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ], [
            'email.unique' => "The entered email already exists in our record",
        ]);

        Newsletter::create([
            'email' => $request->email,
            'is_subscribed' => true,
        ]);

        return response()->json(['success' => 'Subscribed successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $newsletter = Newsletter::find($id);
        return view('admin.pages.newsletter.create', compact('newsletter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $newsletter = Newsletter::find($id);
        $request->validate([
            'email' => 'required|email|unique:newsletters,email,' . $newsletter->id,
        ]);
        try {
            $newsletter->update(['email' => $request->email]);
            return response()->json(['success' => 'Newsletter updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => "Server Error: " . $e->getMessage()], 500);
        }
        // return redirect()->back()->with('success', $id ? 'Newsletter updated successfully!' : 'Newsletter added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $newsletter = Newsletter::find($id);
        $newsletter->delete();
        return response()->json(['success' => 'Newsletter moved to trash!']);
        // return redirect()->back()->with('success', 'Newsletter moved to trash!');
    }

    public function sendMailView(Request $request)
    // $emails = Newsletter::where('is_subscribed', 1)->get();
    {
        $data = $request->except('_');
        return view('admin.pages.newsletter.send-mail-view', compact('data'));
    }

    public function sendMail(Request $request)
    {
        // Basic validation for common fields
        $validated = $request->validate([
            'type' => 'required|in:newsletter,contact,user',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Determine emails based on type
        $emails = match ($validated['type']) {
            'contact', 'user' => collect($request->validate([
                'emails' => 'required|array|min:1',
                'emails.*' => 'required|email|distinct|not_in:select_all',
            ])['emails'])->unique(),

            'newsletter' => DB::table('newsletters')
                ->where('is_subscribed', true)
                ->pluck('email')
                ->unique(),

            default => collect(),
        };

        $failed = [];
        // $emails = [
        //     'acb@exapm.sd',
        //     'acb@exapm.sda',
        //     'acb@exapm.sdss'
        // ];
        foreach ($emails as $email) {
            try {
                $this->universalSendMail(
                    $email,
                    $validated['subject'],
                    [
                        'message' => $validated['message'],
                    ],
                    'admin.emails.send-newsletter'
                );
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$email}: " . $e->getMessage());
                $failed[] = $email;
            }
        }

        return response()->json([
            'success' => $validated['type'] === 'newsletter'
                ? 'Newsletter sent successfully!'
                : 'Message sent successfully!',
            'failed' => $failed,
        ]);
    }
}
