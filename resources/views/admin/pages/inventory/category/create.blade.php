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
            <label class="form-label">Images</label>
            <div id="dropzone-area" class="dropzone"></div>
        </div>

        <!-- Already Uploaded Images -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid mt-5">
            @isset($category->media)
                <div class="col" id="media-col-id-{{ $category->media->id }}">
                    <div class="position-relative setting-input-group border h-100">
                        <button class="btn btn-light position-absolute delete-btn" type="button"
                            onclick="deleteCategoryMedia({{ $category->media->id }}, '{{ route('admin.inventory.category.destroy', $category->media->id) }}')"
                            style="right: 0; background-color: #0000006e;">
                            <i class="bx bx-trash"></i>
                        </button>
                        @if ($category->media->media_type == 'image')
                            <img src="{{ asset($category->media->path) }}" class="card-img-top h-100" alt="...">
                        @else
                            <video src="{{ asset($category->media->path) }}" controls class="card-img-top h-100"></video>
                        @endif
                    </div>
                </div>
            @endisset
        </div>

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
        let selectedFile = null; // Store the selected file

        let myDropzone = new Dropzone("#dropzone-area", {
            url: "#", // Prevent auto-upload
            maxFiles: 1,
            acceptedFiles: "image/*",
            addRemoveLinks: true,
            autoProcessQueue: false,
            init: function() {
                this.on("addedfile", function(file) {
                    selectedFile = file; // Save the selected file
                });
                this.on("removedfile", function() {
                    selectedFile = null; // Remove file reference when deleted
                });
            }
        });
        window.getSelectedFile = function() {
            return selectedFile;
        }

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
