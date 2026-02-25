<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with('user:id,name,email')
                ->select(['id', 'user_id', 'tracking_number', 'customer_name', 'customer_email', 'total', 'status', 'payment_method', 'created_at'])
                ->orderByDesc('created_at');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $viewBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Order #' . $row->id . '\', \'\', `' . route('admin.orders.show', $row->id) . '`)" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    return $viewBtn;
                })
                ->editColumn('created_at', fn ($row) => $row->created_at?->format('d M Y, H:i'))
                ->editColumn('total', fn ($row) => number_format($row->total, 0) . ' PKR')
                ->editColumn('status', fn ($row) => '<span class="badge bg-' . ($row->status === 'pending' ? 'warning' : ($row->status === 'shipped' ? 'success' : 'secondary')) . '">' . ucfirst($row->status ?? '—') . '</span>')
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.pages.orders.index');
    }

    public function show(Order $order)
    {
        $order->load('items');
        return view('admin.pages.orders.show', compact('order'));
    }
}
