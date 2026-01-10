<div class="card-body">
    <form class="row g-3" id="category-form"
        action="{{ isset($category) ? route('admin.inventory.category.update', $category->id) : route('admin.inventory.category.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($category))
            @method('PUT')
        @endif

        <!-- Parent Category -->
        <div class="col-md-12">
            <label for="parent_id" class="form-label">Parent Category</label>
            <select class="form-control select2 w-100" name="parent_id" data-placeholder="Choose Parent Category">
                <option value="">-- No Parent --</option>
                @foreach ($categories as $parentCategory)
                    <option value="{{ $parentCategory->id }}" @selected(isset($category) && $category->parent_id == $parentCategory->id)>
                        {{ $parentCategory->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Name -->
        <div class="col-md-12">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" onkeyup="makeSlug(this.value,'#slug')"
                value="{{ isset($category) ? $category->name : '' }}" required>
        </div>

        <!-- Slug -->
        <div class="col-md-12">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" readonly
                value="{{ isset($category) ? $category->slug : '' }}" required>
        </div>

        <!-- Description -->
        <div class="col-md-12">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" rows="6" class="form-control myEditor">{{ isset($category) ? $category->description : '' }}</textarea>
        </div>

        <!-- Status -->
        <div class="col-md-12">
            <label class="form-label d-block">Status</label>
            <input type="hidden" name="status" value="0">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status-switch" name="status" value="1"
                    {{ isset($category) ? ($category->status ? 'checked' : '') : 'checked' }}>
                <label class="form-check-label" for="status-switch" id="status-switch-label">
                    {{ isset($category) ? ($category->status ? 'Active' : 'Inactive') : 'Active' }}
                </label>
            </div>
        </div>

        <!-- Dropzone Image Upload -->
        <div class="col-md-12">
            <label class="form-label">Images (First image will be featured, max 2 images)</label>
            <div id="dropzone-area" class="dropzone"></div>
        </div>

        <!-- Already Uploaded Images -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid mt-5">
            @isset($category->media)
                @foreach ($category->media->sortByDesc('is_featured') as $media)
                    <div class="col" id="media-col-id-{{ $media->id }}">
                        <div class="position-relative setting-input-group border">
                            <button class="btn btn-light position-absolute delete-btn" type="button"
                                onclick="deleteCategoryMedia({{ $media->id }}, '{{ route('admin.inventory.category.destroy', $media->id) }}')"
                                style="right: 0; top: 0; background-color: #0000006e; z-index: 2;">
                                <i class="bx bx-trash"></i>
                            </button>
                            @if ($media->media_type == 'image')
                                <img src="{{ asset($media->path) }}" class="card-img-top" alt="..." style="height: 200px; object-fit: cover; width: 100%;">
                            @else
                                <video src="{{ asset($media->path) }}" controls class="card-img-top" style="height: 200px; width: 100%;"></video>
                            @endif
                            <div class="p-2 text-center">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input featured-radio" type="radio" name="featured_media_id" 
                                        id="featured_{{ $media->id }}" value="{{ $media->id }}"
                                        {{ $media->is_featured == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured_{{ $media->id }}" style="cursor: pointer;">
                                        Featured
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endisset
        </div>
        
        <!-- Hidden input for new files featured selection -->
        <input type="hidden" name="new_featured_index" id="new-featured-index" value="0">

        <!-- Submit Button -->
        <div class="col-12">
            <button type="submit" id="category-btn" class="btn btn-light mt-3 px-5">
                {{ isset($category) ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>
<script>
    function makeSlug(value, slugColumn) {
        let slug = value.toLowerCase().replace(/\s+/g, '-');
        $(slugColumn).val(slug);
    }

    $(function() {
        // CKEditor initialization
        $('.myEditor').each(function(index) {
            var elementId = $(this).attr('id') || 'editor-' + index;
            $(this).attr('id', elementId);
            CKEDITOR.replace(elementId, {
                width: '100%'
            });
        });

        Dropzone.autoDiscover = false;
        let selectedFiles = []; // Store the selected files

        let myDropzone = new Dropzone("#dropzone-area", {
            url: "#", // Prevent auto-upload
            maxFiles: 2,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            autoProcessQueue: false,
            init: function() {
                this.on("addedfile", function(file) {
                    selectedFiles.push(file); // Save the selected file
                    // Create radio button for new file
                    const fileIndex = selectedFiles.length - 1;
                    const previewElement = file.previewElement;
                    
                    // Add featured radio button below the preview
                    if (!$(previewElement).find('.new-featured-radio').length) {
                        const radioHtml = `
                            <div class="p-2 text-center border-top">
                                <div class="form-check d-inline-block">
                                    <input class="form-check-input new-featured-radio" type="radio" name="new_featured_file" 
                                        id="new_featured_${fileIndex}" value="${fileIndex}" ${fileIndex === 0 ? 'checked' : ''}>
                                    <label class="form-check-label" for="new_featured_${fileIndex}" style="cursor: pointer;">
                                        Featured
                                    </label>
                                </div>
                            </div>
                        `;
                        $(previewElement).find('.dz-preview').append(radioHtml);
                    }
                });
                this.on("removedfile", function(file) {
                    const index = selectedFiles.indexOf(file);
                    selectedFiles = selectedFiles.filter(f => f !== file); // Remove file reference when deleted
                    // Update featured index if needed
                    if ($('#new-featured-index').val() == index) {
                        $('#new-featured-index').val(0);
                    }
                });
            }
        });
        window.getSelectedFiles = function() {
            return selectedFiles;
        }
        
        // Handle new file featured selection
        $(document).on('change', '.new-featured-radio', function() {
            $('#new-featured-index').val($(this).val());
            // Uncheck existing featured radios
            $('input[name="featured_media_id"]').prop('checked', false);
        });
        
        // Handle existing image featured selection
        $(document).on('change', '.featured-radio', function() {
            // Uncheck new file featured radios
            $('.new-featured-radio').prop('checked', false);
            $('#new-featured-index').val('');
        });

        // Form submission handler to append Dropzone files
        $('#category-form').off('submit').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Sync CKEditor content
            $('.myEditor').each(function() {
                var instance = CKEDITOR.instances[$(this).attr('id')];
                if (instance) {
                    instance.updateElement();
                }
            });

            let formData = new FormData(this);
            
            // Get featured media ID from existing images
            const featuredMediaId = $('input[name="featured_media_id"]:checked').val();
            if (featuredMediaId) {
                formData.append('featured_media_id', featuredMediaId);
            }
            
            // Append Dropzone files
            const files = window.getSelectedFiles();
            const newFeaturedIndex = $('#new-featured-index').val() || 0;
            files.forEach((file, index) => {
                formData.append('files[]', file);
                // Mark first new file as featured if no existing featured is selected
                if (!featuredMediaId && index == newFeaturedIndex) {
                    formData.append('new_featured_index', index);
                }
            });

            // Submit form with files
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        successMessage(response.success);
                        $('#custom-lg-modal').modal('hide');
                        if (typeof loadDatatable === 'function') {
                            loadDatatable();
                        } else {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    } else if (response.errors) {
                        errorMessage(Array.isArray(response.errors) ? response.errors.join('<br>') : response.errors);
                    }
                },
                error: function(xhr) {
                    // Only show error if status is not 200/201
                    if (xhr.status === 422) {
                        // Validation errors
                        let errorMsg = 'Validation failed';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        errorMessage(errorMsg);
                    } else if (xhr.status >= 400) {
                        // Other errors
                        let errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg = Object.values(errors).flat().join('<br>');
                        }
                        errorMessage(errorMsg);
                    }
                }
            });
        });

        // Delete category media function
        window.deleteCategoryMedia = function(mediaId, url) {
            confirmDelete(() => {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        successMessage(response.success);
                        $('#media-col-id-' + mediaId).remove();
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete image';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        errorMessage(errorMsg);
                    }
                });
            });
        };

        // Status switch label update
        $('#status-switch').on('change', function() {
            $('#status-switch-label').text($(this).is(':checked') ? 'Active' : 'Inactive');
        });

        // Select2 initialization
        $('select').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: $(this).data('width') ? $(this).data('width') : ($(this).hasClass(
                        'w-100') ? '100%' : 'style'),
                    placeholder: $(this).data('placeholder'),
                    allowClear: Boolean($(this).data('allow-clear')),
                });
            }
        });
    });
</script>
