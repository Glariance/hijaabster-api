<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Coupon::orderBy('status', 'Desc')->orderBy('id', 'Desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    return defaultBadge(config('constants.status.' . $row->status), 25);
                })
                ->editColumn('discount_type', function ($row) {
                    return $row->discount_type === 'percentage' ? $row->discount_value . '%' : 'PKR ' . number_format($row->discount_value, 2);
                })
                ->editColumn('valid_from', function ($row) {
                    return $row->valid_from ? $row->valid_from->format('d M Y') : '-';
                })
                ->editColumn('valid_until', function ($row) {
                    return $row->valid_until ? $row->valid_until->format('d M Y') : '-';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.coupon.show', $row->id);
                    $editUrl = route('admin.coupon.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Coupon Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Coupon Details\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.coupon.destroy', $row->id) . '`)" class="btn btn-light text-danger"><i class="bx bx-trash"></i></a>';
                    return $showBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.pages.coupon.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please check the form.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            Coupon::create($validator->validated());
            DB::commit();
            return response()->json(['success' => "Coupon created successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => 'An error occurred while creating the coupon.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.pages.coupon.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.pages.coupon.create', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed. Please check the form.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            $coupon->update($validator->validated());
            DB::commit();
            return response()->json(['success' => "Coupon updated successfully."]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->getMessage(),
                'message' => 'An error occurred while updating the coupon.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $coupon = Coupon::findOrFail($id);
            $coupon->delete();
            DB::commit();
            return response()->json(['success' => 'Coupon deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => 'Failed to delete coupon: ' . $e->getMessage()], 500);
        }
    }
}
