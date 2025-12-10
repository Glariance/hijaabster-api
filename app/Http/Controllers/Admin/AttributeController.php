<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = ProductAttribute::orderBy('status', 'desc')->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.inventory.attributes.show', $row->id);
                    $editUrl = route('admin.inventory.attributes.edit', $row->id);

                    $showBtn = '<a href="javascript:;" onclick="showAjaxModal(\'View Attribute Details\', \'view\', \'' . $showUrl . '\')" class="btn btn-light"><i class="lni lni-eye"></i></a>';
                    $editBtn = '<a href="javascript:;" onclick="showAjaxModal(\'Edit Attribute Details\', \'Update\', \'' . $editUrl . '\')" class="btn btn-light"><i class="bx bx-edit-alt"></i></a>';
                    $deleteBtn = '<a href="javascript:;" onclick="deleteTag(' . $row->id . ', `' . route('admin.inventory.attributes.destroy', $row->id) . '`)" class="btn btn-light"><i class="bx bx-trash"></i></a>';
                    // $deleteBtn
                    return $editBtn;
                })
                ->addColumn('options', function ($row) {
                    // Start the unordered list
                    $dataUL = '<ul>';

                    // Loop through each option in the options array
                    foreach ($row->options as $option) {
                        // Add each option value to the list item
                        $dataUL .= '<li>' . htmlspecialchars($option->value) . '</li>';
                    }

                    // Close the unordered list
                    $dataUL .= '</ul>';

                    // Return the complete HTML for the options
                    return $dataUL;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->editColumn('status', function ($row) {
                    return defaultBadge(config('constants.status.' . $row->status), 25);
                })
                ->rawColumns(['action', 'status', 'options'])
                ->make(true);
        }
        return view('admin.pages.inventory.attribute.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.inventory.attribute.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'options' => 'required|array|min:1',
            'options.*' => 'required|string|max:255'
        ]);
        try {
            DB::transaction(function () use ($request) {
                $attribute = ProductAttribute::create([
                    'name' => $request->name,
                    'status' => 1,
                ]);
                foreach ($request->options as $option) {
                    ProductAttributeOption::create([
                        'attribute_id' => $attribute->id,
                        'value' => $option,
                    ]);
                }
            });
            return response()->json(['success' => 'Attribute created successfully.']);
        } catch (Exception $ed) {
            return response()->json(['errors' => $ed->getMessage()], 500);
        }
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
        $attribute = ProductAttribute::with('options')->findOrFail($id);
        return view('admin.pages.inventory.attribute.create', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'options' => 'required|array|min:1',
            'options.*' => 'required|string|max:255'
        ]);
        try {
            DB::transaction(function () use ($request, $id) {
                $attribute = ProductAttribute::findOrFail($id);
                $attribute->update([
                    'name' => $request->name,
                ]);
                $optionIds = $request->option_ids ?? [];
                $optionValues = $request->options ?? [];
                foreach ($optionValues as $i => $value) {
                    $optionId = $optionIds[$i] ?? null;
                    if ($optionId) {
                        ProductAttributeOption::where('id', $optionId)->update([
                            'value' => $value,
                        ]);
                    } else {
                        ProductAttributeOption::create([
                            'attribute_id' => $attribute->id,
                            'value' => $value,
                        ]);
                    }
                }
            });
            return response()->json(['success' => 'Attribute updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
