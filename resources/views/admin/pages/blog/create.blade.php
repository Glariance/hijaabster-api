<div class="card-body">
    <form class="row g-3" id="blog-form"
        action="{{ isset($blog) ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @if (isset($blog))
            @method('PUT')
        @endif


        <!-- Tags -->
        <div class="col-md-12">
            <label for="tags" class="form-label">Select Tags</label>
            <select class="form-control select2 w-100" name="tags[]" data-placeholder="Choose Tags for Blogs" multiple>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(isset($blog) && in_array($tag->id, $blog->tags->pluck('id')->toArray()))>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Title -->
        <div class="col-md-12">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" class="form-control" onkeyup="makeSlug(this.value,'#slug')"
                value="{{ isset($blog) ? $blog->title : '' }}" required>
        </div>

        <!-- Slug -->
        <div class="col-md-12">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" name="slug" id="slug" class="form-control" readonly
                value="{{ isset($blog) ? $blog->slug : '' }}" required>
        </div>

        <!-- Meta Title -->
        <div class="col-md-12">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" name="meta_title" class="form-control"
                value="{{ isset($blog) ? $blog->meta_title : '' }}">
        </div>

        <!-- Meta Keyword -->
        <div class="col-md-12">
            <label for="meta_keyword" class="form-label">Meta Keywords</label>
            <input type="text" name="meta_keyword" class="form-control"
                value="{{ isset($blog) ? $blog->meta_keyword : '' }}">
        </div>

        <!-- Meta Description -->
        <div class="col-md-12">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea name="meta_description" rows="3" class="form-control">{{ isset($blog) ? $blog->meta_description : '' }}</textarea>
        </div>

        <!-- Description -->
        <div class="col-md-12">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" rows="6" class="form-control myEditor">{{ isset($blog) ? $blog->description : '' }}</textarea>
        </div>

        <!-- Status -->
        <div class="col-md-12">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="draft" @selected(isset($blog) && $blog->status === 'draft')>Draft</option>
                <option value="published" @selected(isset($blog) && $blog->status === 'published')>Published</option>
                <option value="archived" @selected(isset($blog) && $blog->status === 'archived')>Archived</option>
            </select>
        </div>

        <!-- Dropzone Image Upload -->
        <div class="col-md-12">
            <label class="form-label">Images</label>
            <div id="dropzone-area" class="dropzone"></div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid mt-5">
            @isset($blog->media)

                @foreach ($blog->media as $media)
                    <div class="col" id="media-col-id-{{ $media->id }}">
                        <div class="position-relative setting-input-group border h-100">
                            <button class="btn btn-light position-absolute delete-btn" type="button"
                                onclick="deleteBlogMedia({{ $media->id }}, '{{ route('admin.blogs.destroyMedia', $media->id) }}')"
                                style="right: 0; background-color: #0000006e;">
                                <i class="bx bx-trash"></i>
                            </button>
                            @if ($media->media_type == 'image')
                                <img src="{{ asset($media->path) }}" class="card-img-top h-100" alt="...">
                            @else
                                <video src="{{ asset($media->path) }}" controls class="card-img-top h-100"></video>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endisset
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <button type="submit" id="blog-btn" class="btn btn-light mt-3 px-5">
                {{ isset($blog) ? 'Update' : 'Save' }}
            </button>
        </div>
    </form>
</div>

<!-- Dropzone & CKEditor Script -->
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

        // Dropzone setup
        Dropzone.autoDiscover = false;

        let selectedFiles = [];

        let myDropzone = new Dropzone("#dropzone-area", {
            url: "#", // Prevent auto upload
            maxFiles: 10,
            acceptedFiles: "image/*, video/*",
            addRemoveLinks: true,
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 10,
            init: function() {
                this.on("addedfile", function(file) {
                    selectedFiles.push(file);
                });
                this.on("removedfile", function(file) {
                    selectedFiles = selectedFiles.filter(f => f.name !== file.name);
                });
            }
        });

        // If you need access to the files before submission
        window.getSelectedFile = function() {
            return selectedFiles;
        };

        $('select').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                        'w-100') ? '100%' : 'style',
                    placeholder: $(this).data('placeholder'),
                    allowClear: Boolean($(this).data('allow-clear')),
                });
            }
        });
        // Optional: Handle form submit manually to send Dropzone files along with form data
        // $("#blog-form").submit(function (e) {
        //     e.preventDefault();
        //     const formData = new FormData(this);
        //     selectedFiles.forEach(file => {
        //         formData.append("images[]", file); // name="images[]" in your controller
        //     });

        //     // Send formData using AJAX or custom logic
        // });
    });
</script>
