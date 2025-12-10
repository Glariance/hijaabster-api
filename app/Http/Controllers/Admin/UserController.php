<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('role')->whereNotNull('role_id')->where('role_id', '!=', config('constants.ADMIN'))->select(['id', 'name', 'email', 'role_id', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role', fn($row) => $row->role->name ?? 'User')
                ->addColumn('action', function ($row) {
                    $sendBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Send Mail\', \'Send\', `' . route('admin.newsletter-management.send-mail-view', ['email' => $row->email, 'type' => 'user']) . '`)" class="btn btn-light"><i class="lni lni-envelope"></i></a>';
                    $viewBtn = '<a href="' . route('admin.users.show', $row->id) . '" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteUser(' . $row->id . ', `' . route('admin.users.destroy', $row->id) . '`)" class="btn btn-light"><i class="bx bx-trash"></i></a>';
                    return $sendBtn . ' ' . $viewBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', fn($row) => $row->created_at?->format('d M Y'))
                ->rawColumns(['action'])
                ->make(true);
        }

        $users = User::orderByDesc('created_at')->get();
        return view('admin.pages.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.pages.users.show', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
