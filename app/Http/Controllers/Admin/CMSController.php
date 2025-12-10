<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsPageSection;
use App\Models\CmsPageSectionField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CMSController extends Controller
{
    public function index($slug)
    {
        $page = CmsPage::where('page_slug', $slug)->first();
        if (!$page) {
            return redirect()->route('admin.dashboard')->with('error', "The page you are looking for is not available.");
        }
        // dd($slug);
        return view('admin.pages.cms.index', compact('page', 'slug'));
    }

    public function pageCreate(Request $request)
    {
        $page = CmsPage::where('page_slug', $request->slug)->first();
        return view('admin.pages.cms.page-create', compact('page'));
    }
    public function pagePost(Request $request)
    {
        $pageId = CmsPage::where('id', $request->id)->value('id'); // Get existing ID if updating

        $request->validate([
            'page_title'            => ['required', 'string', 'max:255', Rule::unique('cms_pages', 'page_title')->ignore($pageId)],
            'page_slug'             => ['required', 'string', 'max:255', Rule::unique('cms_pages', 'page_slug')->ignore($pageId)],
            'page_meta_title'       => 'nullable|string|max:255',
            'page_meta_keyword'     => 'nullable|string',
            'page_meta_description' => 'nullable|string',
        ]);

        try {
            $cms = CmsPage::updateOrCreate(
                ['id' => $pageId],
                $request->except('_token')
            );
            return response()->json([
                'success' => $cms->wasRecentlyCreated ? "Page Created Successfully" : "Page Updated Successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
    public function pageDelete($id)
    {
        try {
            $page = CmsPage::find($id);
            if (!$page) {
                return response()->json(['errors' => "Page Not Found"], 404);
            }
            // foreach ($page->sections as $section) {
            //     foreach ($section->fields as $fields) {
            //         $fields->delete();
            //     }
            //     $section->delete();
            // }
            $page->delete();
            return response()->json(['success' => "Page Deleted Successfully"]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
    public function sectionCreate(Request $request)
    {
        $page = CmsPage::find($request->pageId);
        $section = CmsPageSection::find($request->id);
        return view('admin.pages.cms.section-create', compact('page', 'section'));
    }
    public function sectionPost(Request $request)
    {
        $request->validate([
            'cms_page_id'           => 'required|exists:cms_pages,id',
            'section_name'          => 'required|string|max:255',
            'section_type'          => 'required|in:single,repeater',
            'section_sort_order'    => 'required|integer|min:0',
        ]);

        try {
            $cms = CmsPageSection::updateOrCreate(
                ['id' => $request->id],
                $request->except('_token')
            );
            return response()->json([
                'success' => $cms->wasRecentlyCreated ? "Section Created Successfully" : "Section Updated Successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }
    public function sectionDelete($id)
    {
        try {
            $pageSection = CmsPageSection::find($id);
            if (!$pageSection) {
                return response()->json(['errors' => "Page Not Found"], 404);
            }
            // foreach ($page->sections as $section) {
            //     foreach ($section->fields as $fields) {
            //         $fields->delete();
            //     }
            //     $section->delete();
            // }
            $pageSection->delete();
            return response()->json(['success' => "Page Deleted Successfully"]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function sectionFieldIndex($sectionId)
    {
        try {
            $section = CmsPageSection::with('fields')->findOrFail($sectionId);

            if ($section->section_type == 'single') {
                return view('admin.pages.cms.section_fields_single', compact('section'))->render();
            } elseif ($section->section_type == 'repeater') {
                return view('admin.pages.cms.section_fields_repeater', compact('section'))->render();
            } else {
                return response()->json(['error' => 'Only single-type sections are supported'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sectionFieldStore(Request $request, $sectionId)
    {

        // Ensure fields is always an array
        $fields = $request->fields ?? [];
        // dd($request->all());
        $section = CmsPageSection::findOrFail($sectionId);

        if ($section->section_type !== 'single') {
            return response()->json(['error' => 'Cannot add fields to non-single sections'], 403);
        }

        DB::beginTransaction();
        try {
            // Update Existing Fields
            foreach ($fields as $fieldId => $data) {
                $field = CmsPageSectionField::where('cms_page_section_id', $sectionId)
                    ->where('id', $fieldId)
                    ->first();

                if ($field) {
                    if ($field->field_type === 'image' && $request->hasFile("fields.$fieldId")) {
                        $data = $request->file("fields.$fieldId")->store('cms_sections', 'public');
                    }
                    $field->update(['field_value' => $data]);
                }
            }

            // Handle New Fields
            if ($request->has('new_fields')) {
                foreach ($request->new_fields as $uniqueId => $newField) {
                    CmsPageSectionField::create([
                        'cms_page_section_id' => $section->id,
                        'field_group' => null, // Not using repeatable fields
                        'field_name' => $newField['name'],
                        'field_type' => $newField['type'],
                        'field_value' => ($newField['type'] === 'image') ? null : '', // Set default value correctly
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => 'Fields updated successfully', 'section_id' => $section->id], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to update fields',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sectionFieldDestroy($id)
    {
        $field = CmsPageSectionField::findOrFail($id);
        $cmsSectionId = $field->cms_page_section_id;
        $field->delete();

        return response()->json(['success' => 'Field deleted successfully', 'section_id' => $cmsSectionId]);
    }

    public function sectionGroupFieldStore(Request $request, $sectionId)
    {
        try {
            $section = CmsPageSection::findOrFail($sectionId);

            $deletedGroups = [];
            // Delete selected groups (remove all fields in those groups)
            if ($request->filled('delete_groups')) {
                foreach ((array) $request->input('delete_groups') as $groupName) {
                    CmsPageSectionField::where('cms_page_section_id', $sectionId)
                        ->where('field_group', $groupName)
                        ->delete();
                    $deletedGroups[] = $groupName;
                }
            }

            // Update existing fields
            // dd($request->fields);
            if ($request->has('fields')) {
                foreach ($request->fields as $fieldId => $value) {
                    $field = CmsPageSectionField::find($fieldId);
                    if ($field) {
                        // Handle file uploads
                        if ($field->field_type === 'image' && $request->hasFile("fields.$fieldId")) {
                            $file = $request->file("fields.$fieldId");
                            $filename = time() . '_' . $file->getClientOriginalName();
                            $filePath = $file->storeAs('cms_fields', $filename, 'public');
                            $field->field_value = $filePath;
                        } else {
                            $field->field_value = $value;
                        }
                        $field->save();
                    }
                }
            }

            // Add new groups
            if ($request->has('new_groups')) {
                foreach ($request->new_groups as $groupId => $groupName) {
                    foreach ($request->new_fields[$groupId] ?? [] as $fieldData) {
                        CmsPageSectionField::create([
                            'cms_page_section_id'  => $sectionId,
                            'field_group' => $groupName,
                            'field_name'  => $fieldData['name'],
                            'field_type'  => $fieldData['type'],
                            'field_value' => null,
                        ]);
                    }
                }
            }

            return response()->json(['success' => "Group & Fields updated successfully", 'section_id' => $section->id], 200); //->back()->with('success', 'Fields updated successfully.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sectionGroupFieldDelete(Request $request, $sectionId)
    {
        try {
            $section = CmsPageSection::findOrFail($sectionId);
            $request->validate([
                'group_name' => 'required|string'
            ]);

            $groupName = $request->input('group_name');

            CmsPageSectionField::where('cms_page_section_id', $sectionId)
                ->where('field_group', $groupName)
                ->delete();

            return response()->json([
                'success' => "Group '{$groupName}' deleted successfully",
                'section_id' => $section->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function sectionGroupFieldCopy($sectionId)
    {
        $pageSection = CmsPageSection::find($sectionId);

        if (!$pageSection || $pageSection->repeaterGroups->isEmpty()) {
            return response()->json(['message' => "No group found for this section."], 404);
        }

        $sectionGroupCount = $pageSection->repeaterGroups->count() + 1;
        $newGroupName = "Group_$sectionGroupCount";

        // Get the latest group from repeaterGroups (assuming the last one is the original group)
        $originalGroup = $pageSection->repeaterGroups->last();

        if (!$originalGroup) {
            return response()->json(['error' => "No original group found."], 404);
        }

        // Get all fields belonging to the original group
        $fields = CmsPageSectionField::where('cms_page_section_id', $sectionId)
            ->where('field_group', $originalGroup->field_group)
            ->get();

        if ($fields->isEmpty()) {
            return response()->json(['error' => "No fields found to copy."], 404);
        }

        // Duplicate fields with new group name
        foreach ($fields as $field) {
            CmsPageSectionField::create([
                'cms_page_section_id' => $field->cms_page_section_id,
                'field_group' => $newGroupName, // New unique group name
                'field_name' => $field->field_name,
                'field_type' => $field->field_type,
                'field_value' => null,
            ]);
        }

        return response()->json([
            'success' => 'Group copied successfully!',
            'new_group' => $newGroupName
        ], 200);
    }

    public function addFieldsInGroup($sectionId)
    {
        $section = CmsPageSection::find($sectionId);
        return view('admin.pages.cms.addFieldsInGroup', compact('section'));
    }
    public function addFieldsInGroupPost(Request $request, $sectionId)
    {
        $section = CmsPageSection::findOrFail($sectionId);
        $request->validate([
            'cms_page_section_id' => 'required|integer|exists:cms_page_sections,id',
            'field_group' => 'required|string|max:255',
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,image',
        ]);
        try {
            if (strtolower($request->field_group) === 'all') {
                foreach ($section->repeaterGroups as $group) {
                    CmsPageSectionField::create([
                        'cms_page_section_id' => $request->cms_page_section_id,
                        'field_group' => $group->field_group,
                        'field_name' => $request->field_name,
                        'field_type' => $request->field_type,
                    ]);
                }
            } elseif (strtolower($request->field_group) === 'public') {
                CmsPageSectionField::create($request->only(['cms_page_section_id', 'field_name','field_type']));
            } else {
                CmsPageSectionField::create($request->all());
            }

            return response()->json([
                'success' => "Field(s) added successfully.",
                'section_id' => $sectionId
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
