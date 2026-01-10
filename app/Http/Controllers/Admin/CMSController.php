<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\CmsPageSection;
use App\Models\CmsPageSectionField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                    // Handle image fields (from Dropzone - path is in hidden field)
                    if ($field->field_type === 'image') {
                        // Get the file path from the hidden input field (from Dropzone upload)
                        $filePath = trim($data ?? '');
                        
                        \Log::info('Processing image field (single section)', [
                            'field_id' => $fieldId,
                            'file_path_from_form' => $filePath,
                            'current_db_value' => $field->field_value,
                            'has_file' => $request->hasFile("fields.$fieldId"),
                        ]);
                        
                        // Check if a new file path was provided (from Dropzone upload)
                        if (!empty($filePath) && $filePath !== 'null' && $filePath !== '') {
                            // New file path from Dropzone (already uploaded)
                            // Verify the file actually exists
                            $fullPath = storage_path('app/public/' . $filePath);
                            
                            if (file_exists($fullPath)) {
                                $oldValue = $field->field_value;
                                
                                // Delete old image file if it exists and is different from new one
                                if (!empty($oldValue) && $oldValue !== $filePath && Storage::disk('public')->exists($oldValue)) {
                                    Storage::disk('public')->delete($oldValue);
                                    \Log::info('Deleted old image file when updating field (single section)', [
                                        'field_id' => $fieldId,
                                        'old_path' => $oldValue,
                                        'new_path' => $filePath
                                    ]);
                                }
                                
                                $field->field_value = $filePath;
                                $field->save();
                                
                                \Log::info('Image field updated successfully (single section)', [
                                    'field_id' => $fieldId,
                                    'old_value' => $oldValue,
                                    'new_value' => $field->field_value,
                                    'path' => $filePath,
                                ]);
                            } else {
                                // File doesn't exist, but save the path anyway
                                $oldValue = $field->field_value;
                                $field->field_value = $filePath;
                                $field->save();
                                
                                \Log::warning('Image file not found at path, but path saved to database (single section)', [
                                    'field_id' => $fieldId,
                                    'path' => $filePath,
                                    'full_path' => $fullPath,
                                ]);
                            }
                        } elseif ($request->hasFile("fields.$fieldId")) {
                            // Fallback: Regular file upload (if temp directory works)
                            $file = $request->file("fields.$fieldId");
                            
                            if ($file && $file->isValid()) {
                                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                
                                // Ensure directory exists
                                $directory = 'cms_fields';
                                if (!Storage::disk('public')->exists($directory)) {
                                    Storage::disk('public')->makeDirectory($directory);
                                }
                                
                                $filePath = $file->storeAs($directory, $filename, 'public');
                                
                                // Delete old file if exists
                                if ($field->field_value && Storage::disk('public')->exists($field->field_value)) {
                                    Storage::disk('public')->delete($field->field_value);
                                }
                                
                                $field->field_value = $filePath;
                                $field->save();
                                \Log::info('Image uploaded and saved (single section, regular upload)', ['field_id' => $fieldId, 'path' => $filePath]);
                            }
                        } else {
                            // No new file path provided, keep existing value
                            \Log::info('No file path provided in form for image field (single section)', [
                                'field_id' => $fieldId,
                                'keeping_existing' => $field->field_value
                            ]);
                    }
                    } else {
                        // For non-image fields, update the value
                    $field->update(['field_value' => $data]);
                    }
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
            if ($request->has('fields')) {
                foreach ($request->fields as $fieldId => $value) {
                    $field = CmsPageSectionField::find($fieldId);
                    if ($field) {
                        // Handle file uploads (from Dropzone or regular file input)
                        if ($field->field_type === 'image') {
                            // Get the file path from the hidden input field
                            $filePath = trim($value ?? '');
                            
                            \Log::info('Processing image field', [
                                'field_id' => $fieldId,
                                'file_path_from_form' => $filePath,
                                'current_db_value' => $field->field_value,
                                'has_file' => $request->hasFile("fields.$fieldId"),
                                'all_fields_data' => $request->input('fields'),
                            ]);
                            
                            // Check if a new file path was provided (from Dropzone upload)
                            if (!empty($filePath) && $filePath !== 'null' && $filePath !== '') {
                                // New file path from Dropzone (already uploaded via base64 or regular method)
                                // Verify the file actually exists
                                $fullPath = storage_path('app/public/' . $filePath);
                                
                                \Log::info('Processing image field - checking file', [
                                    'field_id' => $fieldId,
                                    'file_path' => $filePath,
                                    'full_path' => $fullPath,
                                    'file_exists' => file_exists($fullPath),
                                ]);
                                
                                // Save the path to database regardless, but log if file doesn't exist
                                if (file_exists($fullPath)) {
                                    $oldValue = $field->field_value;
                                    
                                    // Delete old image file if it exists and is different from new one
                                    if (!empty($oldValue) && $oldValue !== $filePath && Storage::disk('public')->exists($oldValue)) {
                                        Storage::disk('public')->delete($oldValue);
                                        \Log::info('Deleted old image file when updating field', [
                                            'field_id' => $fieldId,
                                            'old_path' => $oldValue,
                                            'new_path' => $filePath
                                        ]);
                                    }
                                    
                                    $field->field_value = $filePath;
                                    $field->save();
                                    
                                    // Verify it was saved
                                    $field->refresh();
                                    \Log::info('Image field updated successfully', [
                                        'field_id' => $fieldId, 
                                        'old_value' => $oldValue,
                                        'new_value' => $field->field_value,
                                        'path' => $filePath,
                                        'saved_correctly' => $field->field_value === $filePath
                                    ]);
                                } else {
                                    // File doesn't exist, but save the path anyway (might be uploaded but not yet visible)
                                    $oldValue = $field->field_value;
                                    $field->field_value = $filePath;
                                    $field->save();
                                    
                                    \Log::warning('Image file not found at path, but path saved to database', [
                                        'field_id' => $fieldId, 
                                        'path' => $filePath, 
                                        'full_path' => $fullPath,
                                        'directory_exists' => file_exists(dirname($fullPath)),
                                        'directory_listing' => file_exists(dirname($fullPath)) ? implode(', ', array_slice(scandir(dirname($fullPath)), 0, 10)) : 'N/A',
                                        'saved_path' => $field->field_value
                                    ]);
                                }
                            } elseif ($request->hasFile("fields.$fieldId")) {
                                // Regular file upload (if temp directory works)
                            $file = $request->file("fields.$fieldId");
                                
                                if ($file && $file->isValid()) {
                                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                    
                                    // Ensure directory exists
                                    $directory = 'cms_fields';
                                    if (!Storage::disk('public')->exists($directory)) {
                                        Storage::disk('public')->makeDirectory($directory);
                                    }
                                    
                                    $filePath = $file->storeAs($directory, $filename, 'public');
                            $field->field_value = $filePath;
                                    $field->save();
                                    \Log::info('Image uploaded and saved', ['field_id' => $fieldId, 'path' => $filePath]);
                                }
                            } else {
                                \Log::info('No file path provided in form for image field', [
                                    'field_id' => $fieldId,
                                    'file_path' => $filePath,
                                    'keeping_existing' => $field->field_value
                                ]);
                            }
                            // If no new file path provided, keep existing value (don't overwrite)
                        } else {
                            // For non-image fields (text, textarea, etc.), update the value
                            // Handle null/empty values
                            $fieldValue = $value ?? '';
                            
                            \Log::info('Updating non-image field', [
                                'field_id' => $fieldId,
                                'field_type' => $field->field_type,
                                'field_name' => $field->field_name,
                                'old_value' => $field->field_value,
                                'new_value' => $fieldValue,
                                'value_length' => strlen($fieldValue),
                            ]);
                            
                            $field->field_value = $fieldValue;
                            $field->save();
                            
                            // Verify it was saved
                            $field->refresh();
                            \Log::info('Field updated successfully', [
                                'field_id' => $fieldId,
                                'saved_value' => $field->field_value,
                                'saved_correctly' => $field->field_value === $fieldValue
                            ]);
                        }
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

    /**
     * Handle image upload from Dropzone
     */
    public function uploadImage(Request $request)
    {
        // Force JSON response
        $request->headers->set('Accept', 'application/json');
        
        try {
            // Check if file was sent as base64 (fallback method)
            // Try to read from raw POST data first (doesn't require temp files)
            $rawPostData = @file_get_contents('php://input');
            
            // Check if POST data was discarded by PHP
            $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
            if ($contentLength > 0 && empty($rawPostData)) {
                // PHP discarded the POST data - can't proceed
                return response()->json([
                    'success' => false,
                    'error' => 'PHP discarded POST data because it cannot create temporary files. Please fix PHP configuration:',
                    'fix_instructions' => [
                        '1. Edit: ' . (php_ini_loaded_file() ?: 'php.ini'),
                        '2. Add/Update: upload_tmp_dir = "' . storage_path('app/temp_uploads') . '"',
                        '3. Add/Update: upload_max_filesize = 10M',
                        '4. Add/Update: post_max_size = 12M',
                        '5. Restart your PHP server',
                    ],
                    'php_ini_location' => php_ini_loaded_file() ?: 'Unknown',
                ], 400);
            }
            
            if ($rawPostData) {
                $postData = json_decode($rawPostData, true);
                if ($postData && isset($postData['file_data']) && isset($postData['file_name'])) {
                    // Pass old_file_path from postData to handleBase64Upload
                    return $this->handleBase64Upload($request, $postData);
                }
            }
            
            // Fallback to regular input (POST or GET)
            if ($request->has('file_data') && $request->has('file_name')) {
                return $this->handleBase64Upload($request);
            }
            
            // Also check for base64 in query string (GET request fallback when POST fails - for small files only)
            if ($request->query('file_data') && $request->query('file_name')) {
                return $this->handleBase64Upload($request);
            }
            
            // Check for PHP upload errors first
            if (!isset($_FILES['file'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'No file was uploaded. Please check PHP upload_max_filesize (currently: ' . ini_get('upload_max_filesize') . ') and post_max_size (currently: ' . ini_get('post_max_size') . ').',
                    'suggestion' => 'Please update php.ini: upload_tmp_dir = "' . storage_path('app/temp_uploads') . '" and restart your server.',
                ], 400);
            }
            
            if (!isset($_FILES['file']['error']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini (currently: ' . ini_get('upload_max_filesize') . ').',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder. Please edit php.ini and set: upload_tmp_dir = "' . storage_path('app/temp_uploads') . '" then restart your server. File: ' . (php_ini_loaded_file() ?: 'php.ini'),
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk. Please edit php.ini and set: upload_tmp_dir = "' . storage_path('app/temp_uploads') . '" then restart your server. File: ' . (php_ini_loaded_file() ?: 'php.ini'),
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];
                
                $errorCode = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
                $errorMessage = $errorMessages[$errorCode] ?? 'Unknown upload error (Code: ' . $errorCode . ')';
                
                \Log::error('File upload error', [
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'temp_dir' => sys_get_temp_dir(),
                    'temp_dir_writable' => is_writable(sys_get_temp_dir()),
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => $errorMessage,
                ], 400);
            }

            $request->validate([
                'file' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            ], [
                'file.required' => 'Please select an image file.',
                'file.image' => 'The file must be an image.',
                'file.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, webp.',
                'file.max' => 'The image may not be greater than 5MB.',
            ]);

            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'error' => 'No file uploaded.',
                ], 400);
            }

            $file = $request->file('file');
            
            // Check if file upload was successful
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'error' => 'File upload failed: ' . $file->getErrorMessage(),
                ], 400);
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists
            $directory = 'cms_fields';
            $storagePath = storage_path('app/public/' . $directory);
            if (!file_exists($storagePath)) {
                if (!mkdir($storagePath, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to create storage directory. Please check permissions.',
                    ], 500);
                }
            }
            
            // Check if directory is writable
            if (!is_writable($storagePath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Storage directory is not writable. Please check permissions.',
                ], 500);
            }
            
            // Don't delete old file during upload - it will be deleted when form is saved
            // Old file deletion is handled in sectionGroupFieldStore method
            
            $filePath = $file->storeAs($directory, $filename, 'public');
            
            if (!$filePath) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to save file.',
                ], 500);
            }
            
            return response()->json([
                'success' => true,
                'path' => $filePath,
                'url' => asset('storage/' . $filePath),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle base64 encoded file upload (fallback when temp directory fails)
     */
    private function handleBase64Upload(Request $request, $postData = null)
    {
        try {
            // Get data from postData array, input (POST), or query (GET)
            if ($postData) {
                $fileData = $postData['file_data'] ?? null;
                $fileName = $postData['file_name'] ?? null;
            } else {
                $fileData = $request->input('file_data') ?: $request->query('file_data');
                $fileName = $request->input('file_name') ?: $request->query('file_name');
            }
            
            if (!$fileData || !$fileName) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing file_data or file_name parameter.',
                ], 400);
            }
            
            // Decode URL-encoded data if it came from GET request
            if ($request->isMethod('get')) {
                $fileData = urldecode($fileData);
                $fileName = urldecode($fileName);
            }
            
            // Remove data URL prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $fileData, $matches)) {
                $fileData = substr($fileData, strpos($fileData, ',') + 1);
                $extension = $matches[1];
            } else {
                // Assume it's already base64
                $extension = pathinfo($fileName, PATHINFO_EXTENSION) ?: 'jpg';
            }
            
            $imageData = base64_decode($fileData);
            
            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid base64 image data.',
                ], 400);
            }
            
            // Validate it's actually an image
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid image file.',
                ], 400);
            }
            
            // Check if there's an old file to delete (from request or postData)
            $oldFilePath = null;
            if ($postData && isset($postData['old_file_path'])) {
                $oldFilePath = $postData['old_file_path'];
            } else {
                $oldFilePath = $request->input('old_file_path') ?: $request->query('old_file_path');
            }
            
            // Don't delete old file during upload - it will be deleted when form is saved
            // Old file deletion is handled in sectionGroupFieldStore method
            
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $directory = 'cms_fields';
            $storagePath = storage_path('app/public/' . $directory);
            
            if (!file_exists($storagePath)) {
                if (!mkdir($storagePath, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to create storage directory.',
                    ], 500);
                }
            }
            
            $filePath = $directory . '/' . $filename;
            $fullPath = storage_path('app/public/' . $filePath);
            
            // Ensure the directory exists
            $dirPath = dirname($fullPath);
            if (!file_exists($dirPath)) {
                if (!mkdir($dirPath, 0755, true)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to create directory: ' . $dirPath,
                    ], 500);
                }
            }
            
            $bytesWritten = file_put_contents($fullPath, $imageData);
            if ($bytesWritten === false) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to save file to: ' . $fullPath,
                ], 500);
            }
            
            // Verify file was saved
            if (!file_exists($fullPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'File was not saved. Path: ' . $fullPath,
                ], 500);
            }
            
            \Log::info('Base64 image uploaded successfully', [
                'path' => $filePath,
                'full_path' => $fullPath,
                'size' => $bytesWritten,
            ]);
            
            return response()->json([
                'success' => true,
                'path' => $filePath,
                'url' => asset('storage/' . $filePath),
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Base64 upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
