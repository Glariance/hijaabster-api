<div class="card-body">
    <form class="row g-3" id="brand-form"
        action="{{ isset($brand) ? route('admin.inventory.brand.update', $brand->id) : route('admin.inventory.brand.store') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if (isset($brand))
            @method('PUT')
        @endif

        <!-- Name -->
        <div class="col-md-12">
            <label for="name" class="form-label">Brand Name</label>
            <input type="text" name="name" class="form-control" onkeyup="makeSlug(this.value,'#slug')"
                value="{{ isset($brand) ? $brand->name : '' }}" required>
        </div>

        <!-- Slug -->
        <div class="col-md-12">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" readonly
                value="{{ isset($brand) ? $brand->slug : '' }}" required>
        </div>

        <!-- Description -->
        <div class="col-md-12">
            <label for="slug" class="form-label">Desctiption</label>
            <textarea name="description" class="myEditor" cols="30" rows="10">@isset($brand){{ $brand->description }}@endisset</textarea>
            {{-- <input type="text" name="slug" id="slug" class="form-control" readonly
                value="{{ isset($brand) ? $brand->slug : '' }}" required> --}}
        </div>

        <!-- Status -->
        <div class="col-md-12">
            <label class="form-label">Status</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="status" 
                    @checked(isset($brand) ? $brand->status == 1 : true)>
                <label class="form-check-label" for="status">
                    <span id="status-label">{{ isset($brand) ? ($brand->status == 1 ? 'Active' : 'Inactive') : 'Active' }}</span>
                </label>
            </div>
            <input type="hidden" name="status" id="status-hidden" value="{{ isset($brand) ? $brand->status : 1 }}">
        </div>

        <!-- Dropzone Image Upload -->
        <div class="col-md-12">
            <label class="form-label">Images</label>
            <div id="dropzone-area" class="dropzone"></div>
        </div>

        <!-- Already Uploaded Images -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid mt-5">
            @isset($brand->media)
                {{-- @foreach ($brand->media as $brand->media) --}}
                    <div class="col" id="media-col-id-{{ $brand->media->id }}">
                        <div class="position-relative setting-input-group border h-100">
                            <button class="btn btn-light position-absolute delete-btn" type="button"
                                onclick="deleteBrandMedia({{ $brand->media->id }}, '{{ route('admin.inventory.brand.destroy', $brand->media->id) }}')"
                                style="right: 0; background-color: #0000006e;">
                                <i class="bx bx-trash"></i>
                            </button>
                            @if ($brand->media->media_type == 'image')
                                <img src="{{ asset($brand->media->path) }}" class="card-img-top h-100" alt="...">
                            @else
                                <video src="{{ asset($brand->media->path) }}" controls class="card-img-top h-100"></video>
                            @endif
                        </div>
                    </div>
                {{-- @endforeach --}}
            @endisset
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <button type="submit" id="brand-btn" class="btn btn-light mt-3 px-5">
                {{ isset($brand) ? 'Update' : 'Save' }}
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
        // Status switch handler
        $('#status').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('#status-hidden').val(isChecked ? 1 : 0);
            $('#status-label').text(isChecked ? 'Active' : 'Inactive');
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
