<div class="card-body">
    <div class="card-title d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-white">Manage Fields for {{ $section->section_name }}</h5>
        <button type="button" class="btn btn-light px-5 radius-30" onclick="addNewField()">
            <i class="bx bx-plus mr-1"></i> Add Field
        </button>
    </div>
    <hr>

    <form class="row g-3" id="cms-section-field-form"
        action="{{ route('admin.cms.section.field.store', ['sectionId' => $section->id]) }}" method="post">
        @csrf

        @if ($section->fields->isNotEmpty())
            <div class="row">
                @foreach ($section->fields as $field)
                    <div class="col-md-{{ $field->field_type === 'textarea' ? '12' : '6' }}">
                        <div class="form-group">
                            <label>{{ ucfirst($field->field_name) }} ({{ $field->field_type }})</label>

                            <div class="input-group mb-3 setting-input-group">
                                @if ($field->field_type === 'text')
                                    <input type="text" name="fields[{{ $field->id }}]" class="form-control"
                                        value="{{ $field->field_value }}">
                                @elseif($field->field_type === 'textarea')
                                    <textarea name="fields[{{ $field->id }}]" class="form-control myEditor">{{ $field->field_value }}</textarea>
                                @elseif($field->field_type === 'image')
                                    <div class="dropzone-container" style="width: 100%;">
                                        <div id="dropzone-{{ $field->id }}" class="dropzone border rounded bg-light" style="min-height: 120px;">
                                            <div class="dz-message" data-dz-message>
                                                <span>Drop image here or click to upload</span>
                                            </div>
                                        </div>
                                        <input type="hidden" name="fields[{{ $field->id }}]" id="field-{{ $field->id }}" value="{{ $field->field_value }}">
                                        <style>
                                            #dropzone-{{ $field->id }} .dz-preview {
                                                width: 120px !important;
                                                min-width: 120px !important;
                                                margin: 5px;
                                            }
                                            #dropzone-{{ $field->id }} .dz-image {
                                                width: 120px !important;
                                                height: 120px !important;
                                            }
                                            #dropzone-{{ $field->id }} .dz-image img {
                                                width: 120px !important;
                                                height: 120px !important;
                                                object-fit: cover;
                                            }
                                        </style>
                                        @if ($field->field_value)
                                            <div class="mt-2">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-light view-image-btn"
                                                    onclick="openImageModal(`{{ asset('storage/' . $field->field_value) }}`)">
                                                    <i class="bx bx-image"></i> View Current Image
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <button class="btn btn-light delete-btn" type="button"
                                    onclick="deleteField({{ $field->id }}, `{{ route('admin.cms.section.field.delete', ['id' => $field->id]) }}`)">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center" style="font-size: 20px;">
                <p>No fields found. Add new fields below.</p>
            </div>
        @endif
        <div id="newFields"></div>

        <div class="col-12">
            <button type="submit" id="btnSectionField" class="btn btn-light px-5">Save Fields</button>
        </div>
    </form>
</div>
<script>
    function addNewField() {
        let uniqueId = Date.now();
        let newField = `
            <div class="form-group new-setting">
                <div class="row">
                    <div class="col-md-5">
                        <label>Field Name</label>
                        <input type="text" name="new_fields[${uniqueId}][name]" class="form-control" placeholder="Enter field name">
                    </div>
                    <div class="col-md-5">
                        <label>Field Type</label>
                        <select name="new_fields[${uniqueId}][type]" class="form-select">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light mt-4" onclick="removeField(this)">
                            <i class="bx bx-trash me-0"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('newFields').insertAdjacentHTML('beforeend', newField);
    }

    function removeField(button) {
        button.closest('.new-setting').remove();
    }
    $(function() {
        $('.myEditor').each(function(index) {
            var elementId = $(this).attr('id') || 'editor-' + index; // Assign a unique ID if missing
            $(this).attr('id', elementId); // Set the ID if not already present
            CKEDITOR.replace(elementId, {
                width: '100%',
                // removePlugins: ['ExportPdf', 'ExportWord', 'TrackChanges', 'Comments']
            });
        });

        // Initialize Dropzone for image fields
        @foreach ($section->fields->where('field_type', 'image') as $field)
            @if($field->field_type === 'image')
                Dropzone.autoDiscover = false;
                    var dropzone{{ $field->id }} = new Dropzone("#dropzone-{{ $field->id }}", {
                        url: "{{ route('admin.cms.image.upload') }}",
                        method: 'post',
                        paramName: 'file',
                        maxFiles: 1,
                        acceptedFiles: 'image/*',
                        addRemoveLinks: true,
                        autoProcessQueue: false, // Don't auto-upload, wait for form submission
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    init: function() {
                        var myDropzone = this;
                        @if($field->field_value)
                            // Add existing image as a mock file
                            var mockFile = { name: "{{ basename($field->field_value) }}", size: 0 };
                            myDropzone.emit("addedfile", mockFile);
                            myDropzone.emit("thumbnail", mockFile, "{{ asset('storage/' . $field->field_value) }}");
                            myDropzone.emit("complete", mockFile);
                            // Ensure hidden field has the initial value
                            $('#field-{{ $field->id }}').val("{{ $field->field_value }}");
                            console.log('Initialized Dropzone with existing image:', "{{ $field->field_value }}");
                        @endif

                        // Store old file path when a new file is added (before upload starts)
                        myDropzone.on("addedfile", function(file) {
                            // Store the current hidden field value as the old file path on the file object
                            var currentPath = $('#field-{{ $field->id }}').val();
                            file.oldFilePath = (currentPath && currentPath.trim() !== '' && currentPath !== 'null') ? currentPath : null;
                            console.log('File added - stored old file path:', file.oldFilePath);
                            console.log('File will be uploaded when form is saved');
                            
                            // Remove any existing files (only allow one)
                            var filesToRemove = [];
                            for (var i = 0; i < this.files.length; i++) {
                                if (this.files[i] !== file && this.files[i].status !== 'complete') {
                                    filesToRemove.push(this.files[i]);
                                }
                            }
                            // Remove old files (but keep completed/mock files that represent existing images)
                            for (var j = 0; j < filesToRemove.length; j++) {
                                this.removeFile(filesToRemove[j]);
                            }
                        });
                    },
                    sending: function(file, xhr, formData) {
                        // Don't send old_file_path during upload - old file will be deleted when form is saved
                        console.log('Uploading new file - old file will be deleted on form save');
                    },
                success: function(file, response) {
                    console.log('Dropzone upload response:', response);
                    
                    // Handle HTML response with embedded JSON (PHP errors)
                    let actualResponse = response;
                    if (typeof response === 'string' && response.includes('<b>Warning</b>')) {
                        // Extract JSON from HTML response
                        const jsonMatch = response.match(/\{[\s\S]*"success"[\s\S]*\}/);
                        if (jsonMatch) {
                            try {
                                actualResponse = JSON.parse(jsonMatch[0]);
                                console.log('Extracted JSON from HTML response:', actualResponse);
                            } catch (e) {
                                console.error('Failed to parse JSON from HTML:', e);
                            }
                        }
                    }
                    
                    // If response indicates failure, trigger base64 fallback
                    if (actualResponse && !actualResponse.success) {
                        if (actualResponse.error && (
                            actualResponse.error.includes('temporary') || 
                            actualResponse.error.includes('No file was uploaded') ||
                            actualResponse.error.toLowerCase().includes('temp')
                        )) {
                            console.log('Upload failed due to temp directory, triggering base64 fallback');
                            // Trigger error handler which will use base64 fallback
                            file.status = 'error';
                            dropzone{{ $field->id }}.emit('error', file, actualResponse);
                            return;
                        }
                    }
                    
                    if (actualResponse && actualResponse.success && actualResponse.path) {
                        // Get old file path before updating
                        var oldFilePath = $('#field-{{ $field->id }}').val();
                        
                        // Update hidden field with new path
                        $('#field-{{ $field->id }}').val(actualResponse.path);
                        console.log('Image uploaded successfully, path saved to hidden field #field-{{ $field->id }}:', actualResponse.path);
                        console.log('Old file path was:', oldFilePath);
                        
                        // Remove old preview if it exists (from previous upload)
                        var existingPreviews = dropzone{{ $field->id }}.files;
                        for (var i = 0; i < existingPreviews.length; i++) {
                            if (existingPreviews[i] !== file && existingPreviews[i].status !== 'error') {
                                dropzone{{ $field->id }}.removeFile(existingPreviews[i]);
                            }
                        }
                        
                        // Update thumbnail - ensure it displays correctly
                        if (actualResponse.url) {
                            file.previewElement.classList.add("dz-success");
                            
                            // Wait a bit for Dropzone to create the preview structure
                            setTimeout(function() {
                                // Set thumbnail using Dropzone's emit method
                                dropzone{{ $field->id }}.emit("thumbnail", file, actualResponse.url);
                                
                                // Also manually update/create the img element
                                let imgElement = file.previewElement.querySelector('.dz-image img');
                                if (!imgElement) {
                                    // Create img element if it doesn't exist
                                    const dzImage = file.previewElement.querySelector('.dz-image');
                                    if (dzImage) {
                                        imgElement = document.createElement('img');
                                        imgElement.setAttribute('data-dz-thumbnail', '');
                                        dzImage.appendChild(imgElement);
                                    }
                                }
                                
                                if (imgElement) {
                                    imgElement.src = actualResponse.url;
                                    imgElement.style.width = '100%';
                                    imgElement.style.height = '100%';
                                    imgElement.style.objectFit = 'cover';
                                    imgElement.onload = function() {
                                        console.log('Thumbnail image loaded successfully');
                                        file.previewElement.classList.remove("dz-processing");
                                    };
                                    imgElement.onerror = function() {
                                        console.error('Failed to load thumbnail image:', actualResponse.url);
                                    };
                                }
                            }, 100);
                        }
                    } else {
                        console.error('Upload response missing path or success:', actualResponse);
                        file.previewElement.classList.add("dz-error");
                        // Try base64 fallback
                        if (file && file.size > 0) {
                            console.log('Attempting base64 fallback due to invalid response');
                            file.status = 'error';
                            dropzone{{ $field->id }}.emit('error', file, actualResponse || response);
                        } else {
                            alert('Upload failed: ' + (actualResponse?.error || 'Unknown error'));
                        }
                    }
                },
                error: function(file, response) {
                    // If upload fails due to temp directory issue, try base64 fallback
                    let shouldUseFallback = false;
                    
                    if (file.xhr) {
                        const status = file.xhr.status;
                        const responseText = file.xhr.responseText || '';
                        
                        console.log('Dropzone error - Status:', status, 'Response:', responseText.substring(0, 200));
                        
                        // Check for temp directory errors (including PHP startup errors)
                        if (responseText.includes('unable to create a temporary file') || 
                            responseText.includes('Unable to create temporary file') ||
                            responseText.includes('UPLOAD_ERR_NO_TMP_DIR') || 
                            responseText.includes('UPLOAD_ERR_CANT_WRITE') ||
                            responseText.includes('PHP Request Startup') ||
                            responseText.includes('POST data can\'t be buffered') ||
                            responseText.includes('upload_tmp_dir') ||
                            responseText.includes('No file was uploaded') ||
                            (status === 422 && responseText.includes('temporary'))) {
                            shouldUseFallback = true;
                            console.log('Temp directory error detected, triggering base64 fallback');
                        }
                    } else if (response && typeof response === 'object' && response.error) {
                        const errorMsg = response.error.toLowerCase();
                        if (errorMsg.includes('temporary') || errorMsg.includes('temp_dir') || errorMsg.includes('upload_tmp_dir') || errorMsg.includes('no file was uploaded')) {
                            shouldUseFallback = true;
                            console.log('Temp directory error detected in response object, triggering base64 fallback');
                        }
                    }
                    
                    // Also check if file exists but upload failed
                    if (!shouldUseFallback && file && file.status === 'error') {
                        // If we have a file but upload failed, try base64 as fallback
                        shouldUseFallback = true;
                        console.log('Upload failed, trying base64 fallback');
                    }
                    
                    if (shouldUseFallback && file && file.size > 0) {
                        console.log('Starting base64 fallback upload for file:', file.name, 'Size:', file.size);
                        // Try base64 upload as fallback
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            console.log('File read as base64, length:', e.target.result.length);
                            // Get CSRF token from meta tag or cookie
                            let csrfToken = $('meta[name="csrf-token"]').attr('content');
                            if (!csrfToken) {
                                // Try to get from cookie
                                csrfToken = getCookie('XSRF-TOKEN');
                                if (csrfToken) {
                                    csrfToken = decodeURIComponent(csrfToken);
                                }
                            }
                            
                            console.log('Base64 fallback upload - File size:', file.size, 'Base64 length:', e.target.result.length);
                            
                            // Don't send old_file_path during upload - old file will be deleted when form is saved
                            // Use JSON POST with raw data (php://input) which doesn't require temp files
                            // This bypasses PHP's POST buffering that requires temp files
                            const uploadData = {
                                file_data: e.target.result,
                                file_name: file.name
                            };
                            
                            console.log('Base64 upload - old file will be deleted on form save');
                            
                            $.ajax({
                                url: "{{ route('admin.cms.image.upload') }}",
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify(uploadData),
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                success: function(response) {
                                    console.log('Base64 upload response:', response);
                                    
                                    // Handle HTML response with embedded JSON
                                    let actualResponse = response;
                                    if (typeof response === 'string' && response.includes('<b>Warning</b>')) {
                                        const jsonMatch = response.match(/\{[\s\S]*"success"[\s\S]*\}/);
                                        if (jsonMatch) {
                                            try {
                                                actualResponse = JSON.parse(jsonMatch[0]);
                                                console.log('Extracted JSON from HTML response:', actualResponse);
                                            } catch (e) {
                                                console.error('Failed to parse JSON:', e);
                                            }
                                        }
                                    }
                                    
                                    if (actualResponse && actualResponse.success && actualResponse.path) {
                                        $('#field-{{ $field->id }}').val(actualResponse.path);
                                        console.log('Image uploaded successfully, path saved to hidden field #field-{{ $field->id }}:', actualResponse.path);
                                        
                                        // Update thumbnail using Dropzone API
                                        if (actualResponse.url) {
                                            file.previewElement.classList.remove("dz-error");
                                            file.previewElement.classList.add("dz-success");
                                            
                                            // Wait a bit for Dropzone to create the preview structure
                                            setTimeout(function() {
                                                // Set thumbnail using Dropzone's emit method
                                                dropzone{{ $field->id }}.emit("thumbnail", file, actualResponse.url);
                                                
                                                // Also manually update/create the img element
                                                let imgElement = file.previewElement.querySelector('.dz-image img');
                                                if (!imgElement) {
                                                    // Create img element if it doesn't exist
                                                    const dzImage = file.previewElement.querySelector('.dz-image');
                                                    if (dzImage) {
                                                        imgElement = document.createElement('img');
                                                        imgElement.setAttribute('data-dz-thumbnail', '');
                                                        dzImage.appendChild(imgElement);
                                                    }
                                                }
                                                
                                                if (imgElement) {
                                                    imgElement.src = actualResponse.url;
                                                    imgElement.style.width = '100%';
                                                    imgElement.style.height = '100%';
                                                    imgElement.style.objectFit = 'cover';
                                                    imgElement.onload = function() {
                                                        console.log('Thumbnail image loaded successfully');
                                                        file.previewElement.classList.remove("dz-processing");
                                                    };
                                                    imgElement.onerror = function() {
                                                        console.error('Failed to load thumbnail image:', actualResponse.url);
                                                    };
                                                }
                                            }, 100);
                                        }
                                    } else {
                                        console.error('Upload failed:', actualResponse);
                                        file.previewElement.classList.add("dz-error");
                                        
                                        // Show detailed error message with fix instructions
                                        let errorMsg = '';
                                        if (actualResponse && actualResponse.error) {
                                            errorMsg = actualResponse.error;
                                        } else {
                                            errorMsg = 'Upload failed. PHP cannot process file uploads.';
                                        }
                                        
                                        if (actualResponse && actualResponse.fix_instructions) {
                                            errorMsg += '\n\n' + actualResponse.fix_instructions.join('\n');
                                        }
                                        
                                        // Use a more user-friendly alert or SweetAlert if available
                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'error',
                                                iconColor: '#BE446C',
                                                title: 'Upload Failed',
                                                html: errorMsg.replace(/\n/g, '<br>'),
                                                confirmButtonText: 'OK',
                                                width: '600px',
                                                background: '#1a1a1a',
                                                color: '#ffffff',
                                                confirmButtonColor: '#BE446C',
                                                customClass: {
                                                    popup: 'swal2-dark',
                                                    title: 'swal2-dark-title',
                                                    content: 'swal2-dark-content'
                                                }
                                            });
                                        } else {
                                            alert(errorMsg);
                                        }
                                    }
                                },
                                error: function(xhr) {
                                    file.previewElement.classList.add("dz-error");
                                    let errorMsg = 'Upload failed even with fallback method.';
                                    if (xhr.responseJSON && xhr.responseJSON.error) {
                                        errorMsg += ' ' + xhr.responseJSON.error;
                                    }
                                    alert(errorMsg);
                                }
                            });
                        };
                        reader.onerror = function() {
                            file.previewElement.classList.add("dz-error");
                            alert('Failed to read file for fallback upload.');
                        };
                        reader.readAsDataURL(file);
                        return; // Don't show error alert, base64 upload will handle it
                    }
                    
                    file.previewElement.classList.add("dz-error");
                    let errorMessage = 'Unknown error';
                    
                    // Check if response is HTML (PHP error page)
                    if (file.xhr && file.xhr.responseText) {
                        const responseText = file.xhr.responseText;
                        
                        // Check for PHP errors in HTML
                        if (responseText.includes('PHP Request Startup') || responseText.includes('Warning') || responseText.includes('<b>')) {
                            // Extract error message from HTML
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = responseText;
                            const textContent = tempDiv.textContent || tempDiv.innerText || '';
                            
                            // Look for specific error messages
                            if (textContent.includes('unable to create a temporary file')) {
                                errorMessage = 'PHP cannot create temporary file. Using fallback method...';
                            } else if (textContent.includes('upload_max_filesize')) {
                                errorMessage = 'File too large. PHP upload_max_filesize limit exceeded.';
                            } else {
                                // Try to extract JSON from HTML if present
                                const jsonMatch = responseText.match(/\{[\s\S]*\}/);
                                if (jsonMatch) {
                                    try {
                                        const jsonData = JSON.parse(jsonMatch[0]);
                                        errorMessage = jsonData.error || jsonData.message || 'Upload failed';
                                    } catch (e) {
                                        errorMessage = 'PHP Error: ' + (textContent.substring(0, 200) || 'Unable to create temporary file');
                                    }
                                } else {
                                    errorMessage = 'PHP Error: ' + (textContent.substring(0, 200) || 'Unable to create temporary file');
                                }
                            }
                        } else {
                            // Try to parse as JSON
                            try {
                                const parsed = typeof response === 'string' ? JSON.parse(response) : response;
                                errorMessage = parsed.error || parsed.message || 'Upload failed';
                            } catch (e) {
                                if (file.xhr.response) {
                                    try {
                                        const parsed = JSON.parse(file.xhr.response);
                                        errorMessage = parsed.error || parsed.message || 'Upload failed';
                                    } catch (e2) {
                                        errorMessage = file.xhr.responseText || 'Upload failed';
                                    }
                                } else {
                                    errorMessage = responseText || 'Upload failed';
                                }
                            }
                        }
                    } else if (typeof response === 'string') {
                        try {
                            response = JSON.parse(response);
                            errorMessage = response.error || response.message || 'Upload failed';
                        } catch (e) {
                            errorMessage = response;
                        }
                    } else if (response && response.error) {
                        errorMessage = response.error;
                    } else if (response && response.message) {
                        errorMessage = response.message;
                    }
                    
                    console.error('Dropzone upload error:', response, file.xhr);
                    alert('Error uploading image: ' + errorMessage);
                },
                    removedfile: function(file) {
                        $('#field-{{ $field->id }}').val('');
                        if (file.xhr && file.xhr.response) {
                            var response = JSON.parse(file.xhr.response);
                            // Optionally delete file from server
                        }
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    }
                });
            @endif
        @endforeach
        
        // Helper function to get cookie
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
        
        // Handle form submission - upload all pending Dropzone files first
        $('#cms-section-field-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var actionUrl = form.attr("action");
            // Don't collect formData here - it will be collected after uploads complete
            
            // Collect all Dropzone instances that have pending files
            var dropzonesToProcess = [];
            @foreach ($section->fields->where('field_type', 'image') as $field)
                if (dropzone{{ $field->id }}.getQueuedFiles().length > 0) {
                    dropzonesToProcess.push({
                        dropzone: dropzone{{ $field->id }},
                        fieldId: {{ $field->id }},
                        oldFilePath: $('#field-{{ $field->id }}').val()
                    });
                }
            @endforeach
            
            // Function to process all uploads sequentially
            function processUploads(index) {
                if (index >= dropzonesToProcess.length) {
                    // All uploads done, wait a bit to ensure all hidden fields are updated
                    console.log('All uploads complete, waiting 200ms before form submission...');
                    setTimeout(function() {
                        submitForm();
                    }, 200);
                    return;
                }
                
                var item = dropzonesToProcess[index];
                var file = item.dropzone.getQueuedFiles()[0]; // Get the first queued file
                
                if (!file) {
                    // No file to upload for this dropzone, move to next
                    processUploads(index + 1);
                    return;
                }
                
                // Store old file path on the file object
                file.oldFilePath = item.oldFilePath;
                
                // Upload this file
                item.dropzone.processQueue();
                
                // Wait for this upload to complete before processing next
                item.dropzone.on("success", function(file, response) {
                    // Handle HTML response with embedded JSON
                    let actualResponse = response;
                    if (typeof response === 'string' && response.includes('<b>Warning</b>')) {
                        const jsonMatch = response.match(/\{[\s\S]*"success"[\s\S]*\}/);
                        if (jsonMatch) {
                            try {
                                actualResponse = JSON.parse(jsonMatch[0]);
                            } catch (e) {
                                console.error('Failed to parse JSON from HTML:', e);
                            }
                        }
                    }
                    
                    if (actualResponse && actualResponse.success && actualResponse.path) {
                        // Update hidden field with new path
                        $('#field-' + item.fieldId).val(actualResponse.path);
                        console.log('Image uploaded and hidden field updated for field ' + item.fieldId + ':', actualResponse.path);
                        
                        // Verify the hidden field was updated
                        var verifyValue = $('#field-' + item.fieldId).val();
                        console.log('Verifying hidden field value:', verifyValue);
                        if (verifyValue !== actualResponse.path) {
                            console.error('ERROR: Hidden field value mismatch! Expected:', actualResponse.path, 'Got:', verifyValue);
                            // Try to set it again
                            $('#field-' + item.fieldId).val(actualResponse.path);
                            console.log('Retried setting hidden field value');
                        }
                    } else {
                        console.error('Upload response missing path or success:', actualResponse);
                    }
                    
                    // Remove this event listener to avoid multiple calls
                    item.dropzone.off("success");
                    
                    // Small delay to ensure DOM is updated before processing next
                    setTimeout(function() {
                        // Process next upload
                        processUploads(index + 1);
                    }, 100);
                });
                
                item.dropzone.on("error", function(file, response) {
                    console.error('Upload failed for field ' + item.fieldId, response);
                    // Remove this event listener
                    item.dropzone.off("error");
                    // Continue with next upload even if this one failed
                    processUploads(index + 1);
                });
            }
            
            // Function to submit the form after all uploads
            function submitForm() {
                // Update CKEditor content to textarea fields before submitting
                console.log('Updating CKEditor instances before form submission...');
                console.log('Total CKEditor instances:', Object.keys(CKEDITOR.instances).length);
                
                // First, make sure all CKEditor instances are synced
                for (var instance in CKEDITOR.instances) {
                    if (CKEDITOR.instances.hasOwnProperty(instance)) {
                        try {
                            console.log('Updating CKEditor instance:', instance);
                            // Get the content first
                            var content = CKEDITOR.instances[instance].getData();
                            console.log('CKEditor content for', instance, ':', content ? (content.substring(0, 100) + '... (length: ' + content.length + ')') : '(empty)');
                            
                            // Update the element
                            CKEDITOR.instances[instance].updateElement();
                            
                            // Verify the textarea was updated
                            var textareaElement = CKEDITOR.instances[instance].element.$;
                            if (textareaElement) {
                                console.log('Textarea value after sync:', textareaElement.value ? (textareaElement.value.substring(0, 50) + '...') : '(empty)');
                            }
                        } catch (e) {
                            console.error('Error updating CKEditor instance', instance, ':', e);
                        }
                    }
                }
                
                // Also manually sync any textarea with class myEditor that might not have CKEditor
                $('.myEditor').each(function() {
                    var $textarea = $(this);
                    var editorId = $textarea.attr('id');
                    if (editorId && CKEDITOR.instances[editorId]) {
                        // Already handled above
                    } else {
                        console.log('Textarea without CKEditor found:', $textarea.attr('name'), 'value:', $textarea.val() ? ($textarea.val().substring(0, 50) + '...') : '(empty)');
                    }
                });
                
                // Verify all hidden fields for image fields have values
                console.log('Checking hidden fields for image fields before form submission:');
                @foreach ($section->fields->where('field_type', 'image') as $field)
                    var fieldValue{{ $field->id }} = $('#field-{{ $field->id }}').val();
                    console.log('Hidden field #field-{{ $field->id }} value:', fieldValue{{ $field->id }});
                    if (!fieldValue{{ $field->id }} || fieldValue{{ $field->id }}.trim() === '') {
                        console.warn('WARNING: Hidden field #field-{{ $field->id }} is empty!');
                    }
                @endforeach
                
                // IMPORTANT: Re-collect form data AFTER uploads complete to include updated hidden field values
                var formData = new FormData(form[0]);
                console.log('Form data collected AFTER uploads complete');
                
                // Log form data to verify all values are included
                console.log('Form data being submitted:');
                for (var pair of formData.entries()) {
                    var value = pair[1];
                    if (typeof value === 'string' && value.length > 100) {
                        console.log(pair[0] + ':', value.substring(0, 100) + '... (length: ' + value.length + ')');
                    } else {
                        console.log(pair[0] + ':', value);
                    }
                }
                
                $.ajax({
                    url: actionUrl,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    beforeSend: function () {
                        progressLoad();
                        $('#btnSectionField').prop("disabled", true);
                    },
                    success: function (response) {
                        console.log('Form submission response:', response);
                        // Close loading dialog first
                        Swal.close();
                        
                        // Check if response has success message
                        if (response && response.success) {
                            // Show success toast notification
                            Swal.fire({
                                icon: 'success',
                                iconColor: '#10b981', // Green for success icon
                                title: 'Success!',
                                text: response.success || 'Data saved successfully!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#1a1a1a', // Dark background
                                color: '#ffffff', // White text
                                didOpen: (toast) => {
                                    const progressBar = toast.querySelector(".swal2-timer-progress-bar");
                                    if (progressBar) {
                                        progressBar.style.backgroundColor = "#BE446C"; // Brand color for progress bar
                                    }
                                },
                            });
                            // Reload page after 2 seconds to show updated data
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else if (response && response.error) {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#BE446C',
                                title: 'Error',
                                text: response.error || 'Something went wrong!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                background: '#1a1a1a',
                                color: '#ffffff',
                                didOpen: (toast) => {
                                    const progressBar = toast.querySelector(".swal2-timer-progress-bar");
                                    if (progressBar) {
                                        progressBar.style.backgroundColor = "#BE446C";
                                    }
                                },
                            });
                        } else {
                            // Fallback success message
                            Swal.fire({
                                icon: 'success',
                                iconColor: '#10b981',
                                title: 'Success!',
                                text: 'Data saved successfully!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                background: '#1a1a1a',
                                color: '#ffffff',
                                didOpen: (toast) => {
                                    const progressBar = toast.querySelector(".swal2-timer-progress-bar");
                                    if (progressBar) {
                                        progressBar.style.backgroundColor = "#BE446C";
                                    }
                                },
                            });
                            // Reload page after 2 seconds to show updated data
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function (xhr) {
                        let messageError = "Something went wrong!";
                        if (xhr.responseJSON) {
                            let errors = xhr.responseJSON?.message ? xhr.responseJSON?.message : xhr.responseJSON.errors;
                            if (typeof errors === "object") {
                                Object.values(errors).forEach(msg => errorMessage(msg));
                            } else if (typeof errors === "string") {
                                errorMessage(errors);
                            } else {
                                errorMessage(messageError);
                            }
                        } else if (xhr.responseText) {
                            errorMessage(xhr.responseText);
                        } else {
                            errorMessage(messageError);
                        }
                    },
                    complete: function () {
                        // Don't close Swal here - let the success/error handler manage it
                        // Swal.close(); // Removed - let success/error handlers manage Swal
                        $('#btnSectionField').prop("disabled", false);
                    }
                });
            }
            
            // Start processing uploads
            if (dropzonesToProcess.length > 0) {
                processUploads(0);
            } else {
                // No files to upload, submit form directly
                submitForm();
            }
            
            return false;
        });
    });
</script>

