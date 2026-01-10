@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Product Management')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Product Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ isset($product) ? 'Edit Product' : 'Create Product' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" id="product-form"
                        action="{{ isset($product) ? route('admin.inventory.product.update', $product->id) : route('admin.inventory.product.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($product))
                            @method('PUT')
                        @endif

                        <!-- Name -->
                        <div class="col-md-12">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" onkeyup="makeSlug(this.value,'#slug')"
                                value="{{ isset($product) ? $product->name : old('name') }}" placeholder="Pure Serenity Moisturizer" required>
                        </div>

                        <!-- Slug -->
                        <div class="col-md-12">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" readonly
                                value="{{ isset($product) ? $product->slug : old('slug') }}" required>
                        </div>

                        <!-- Category -->
                        <div class="col-md-12">
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled {{ !isset($product) && !old('category_id') ? 'selected' : '' }}>Select category</option>
                                @foreach ($categories as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ (isset($product) && $product->category_id == $parent->id) || old('category_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                    @foreach ($parent->children as $child)
                                        <option value="{{ $child->id }}"
                                            {{ (isset($product) && $product->category_id == $child->id) || old('category_id') == $child->id ? 'selected' : '' }}>
                                            {{ $parent->name }} > {{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="col-md-12">
                            <label for="brand_id" class="form-label">Brand</label>
                            <select name="brand_id" class="form-select">
                                <option value="">Select brand</option>
                                @foreach ($brands ?? [] as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ (isset($product) && $product->brand_id == $brand->id) || old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6">
                            <label for="base_price" class="form-label">Price (PKR)</label>
                            <input type="number" name="base_price" id="base_price" class="form-control" step="0.01" min="0"
                                value="{{ isset($product) ? $product->base_price : old('base_price', 0) }}" required>
                        </div>

                        <!-- Stock -->
                        <div class="col-md-6">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" name="stock" id="stock" class="form-control" min="0"
                                value="{{ isset($product) ? $product->stock : old('stock', 0) }}" required>
                        </div>

                        <!-- Discount -->
                        <div class="col-md-12">
                            <label class="form-label">Discount</label>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="has_discount" name="has_discount" value="1"
                                    {{ (isset($product) && $product->has_discount == 1) || old('has_discount') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_discount">Enable Discount</label>
                            </div>
                            <div id="discount-fields" style="{{ (isset($product) && $product->has_discount == 1) || old('has_discount') ? '' : 'display: none;' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="discount_type" class="form-label">Discount Type</label>
                                        <select name="discount_type" id="discount_type" class="form-select">
                                            <option value="percentage" {{ (isset($product) && $product->discount_type == 'percentage') || old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                            <option value="fixed" {{ (isset($product) && $product->discount_type == 'fixed') || old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (PKR)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="discount_value" class="form-label">Discount Value</label>
                                        <input type="number" name="discount_value" id="discount_value" class="form-control" step="0.01" min="0"
                                            value="{{ isset($product) ? $product->discount_value : old('discount_value', 0) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon -->
                        <div class="col-md-12">
                            <label for="coupon_id" class="form-label">Coupon</label>
                            <select name="coupon_id" id="coupon_id" class="form-select">
                                <option value="">No Coupon</option>
                                @foreach ($coupons ?? [] as $coupon)
                                    <option value="{{ $coupon->id }}"
                                        {{ (isset($product) && $product->coupon_id == $coupon->id) || old('coupon_id') == $coupon->id ? 'selected' : '' }}>
                                        {{ $coupon->code ?? $coupon->name ?? 'Coupon #' . $coupon->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Featured, New, Top -->
                        <div class="col-md-12">
                            <label class="form-label">Product Flags</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                            {{ (isset($product) && $product->featured == 1) || old('featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featured">Featured</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="new" name="new" value="1"
                                            {{ (isset($product) && $product->new == 1) || old('new') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new">New</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="top" name="top" value="1"
                                            {{ (isset($product) && $product->top == 1) || old('top') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="top">Top</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="myEditor" cols="30" rows="10">{{ isset($product) ? $product->description : old('description') }}</textarea>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" 
                                    @checked(isset($product) ? $product->status == 1 : true)>
                                <label class="form-check-label" for="status">
                                    <span id="status-label">{{ isset($product) ? ($product->status == 1 ? 'Active' : 'Inactive') : 'Active' }}</span>
                                </label>
                            </div>
                            <input type="hidden" name="status" id="status-hidden" value="{{ isset($product) ? $product->status : 1 }}">
                        </div>

                        <!-- Dropzone Image Upload -->
                        <div class="col-md-12">
                            <label class="form-label">Images (First image will be featured)</label>
                            <div id="dropzone-area" class="dropzone"></div>
                        </div>

                        <!-- Already Uploaded Images -->
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid mt-5">
                            @isset($product->media)
                                @foreach ($product->media->sortByDesc('is_featured') as $media)
                                    <div class="col" id="media-col-id-{{ $media->id }}">
                                        <div class="position-relative setting-input-group border">
                                            <button class="btn btn-light position-absolute delete-btn" type="button"
                                                onclick="deleteProductMedia({{ $media->id }}, '{{ route('admin.inventory.product.destroy', $media->id) }}')"
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
                            <button type="submit" id="product-btn" class="btn btn-light mt-3 px-5">
                                {{ isset($product) ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function makeSlug(value, slugColumn) {
            let slug = value.toLowerCase().replace(/\s+/g, '-');
            $(slugColumn).val(slug);
        }

        $(function() {
            // Status switch handler
            $('#status').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('#status-hidden').val(isChecked ? 1 : 0);
                $('#status-label').text(isChecked ? 'Active' : 'Inactive');
            });

            // Discount toggle handler
            $('#has_discount').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#discount-fields').show();
                } else {
                    $('#discount-fields').hide();
                }
            });

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
            $('#product-form').off('submit').on('submit', function(e) {
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

                // Submit form with file
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: { 
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            successMessage(response.success);
                            setTimeout(() => {
                                window.location.href = "{{ route('admin.inventory.product.index') }}";
                            }, 1500);
                        } else if (response.errors) {
                            errorMessage(Array.isArray(response.errors) ? response.errors.join('<br>') : response.errors);
                        }
                    },
                    error: function(xhr) {
                        let messageError = "Something went wrong!";
                        if (xhr.responseJSON) {
                            let errors = xhr.responseJSON?.errors || xhr.responseJSON?.message;

                            if (errors) {
                                if (typeof errors === "object" && !Array.isArray(errors)) {
                                    // Handle validation errors object {field: [messages]}
                                    Object.values(errors).forEach(msgArray => {
                                        if (Array.isArray(msgArray)) {
                                            msgArray.forEach(msg => errorMessage(msg));
                                        } else {
                                            errorMessage(msgArray);
                                        }
                                    });
                                } else if (Array.isArray(errors)) {
                                    errors.forEach(msg => errorMessage(msg));
                                } else if (typeof errors === "string") {
                                    errorMessage(errors);
                                } else {
                                    errorMessage(messageError);
                                }
                            } else {
                                errorMessage(messageError);
                            }
                        } else if (xhr.responseText) {
                            // Try to parse HTML response and extract error messages
                            let parser = new DOMParser();
                            let doc = parser.parseFromString(xhr.responseText, 'text/html');
                            let errorElements = doc.querySelectorAll('.error, .alert-danger, ul li');
                            if (errorElements.length > 0) {
                                errorElements.forEach(el => {
                                    let text = el.textContent.trim();
                                    if (text) errorMessage(text);
                                });
                            } else {
                                // If no structured errors found, show generic message
                                errorMessage(messageError);
                            }
                        } else {
                            errorMessage(messageError);
                        }
                    }
                });
            });

            // Delete product media function
            window.deleteProductMedia = function(mediaId, url) {
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
        });
    </script>
@endpush
