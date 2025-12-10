<div class="card-body">
    <form class="row g-3" id="cms-page-form" action="{{ route('admin.cms.page.post') }}" method="POST">
        @csrf
        <div class="col-md-6">
            <label for="page_title" class="form-label">Page Title</label>
            <input type="hidden" name="id" value="{{ isset($page) ? $page->id : '' }}">
            <input type="text" class="form-control" id="page_title" name="page_title"
                value="{{ isset($page) ? $page->page_title : '' }}">
        </div>
        <div class="col-md-6">
            <label for="page_slug" class="form-label">Page Slug (Auto create)</label>
            <input type="text" class="form-control" id="page_slug" name="page_slug"
                value="{{ isset($page) ? $page->page_slug : '' }}" @readonly(true)>
        </div>
        <div class="col-md-6">
            <label for="page_meta_title" class="form-label">Page Meta Title</label>
            <input type="text" class="form-control" id="page_meta_title" name="page_meta_title"
                value="{{ isset($page) ? $page->page_meta_title : '' }}">
        </div>
        <div class="col-md-6">
            <label for="page_meta_keyword" class="form-label">Page Meta Keyword</label>
            <input type="text" class="form-control" id="page_meta_keyword" name="page_meta_keyword"
                value="{{ isset($page) ? $page->page_meta_keyword : '' }}">
        </div>
        <div class="col-md-12">
            <label for="page_meta_description" class="form-label">Page Meta Description</label>
            <textarea class="form-control" name="page_meta_description" id="page_meta_description" cols="30" rows="10">{{ isset($page) ? $page->page_meta_description : '' }}</textarea>
            {{-- <input type="text" class="form-control" id="mail-driver" name="mail_driver" value=""> --}}
        </div>
        <div class="col-12">
            <button type="submit" id="cms-page-btn"
                class="btn btn-light mt-5 px-5">{{ isset($page) ? 'Update' : 'Save' }}</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#page_title').on('keyup', function() {
            let slug = $(this).val().toLowerCase().replace(/\s+/g, '-');
            $('#page_slug').val(slug);
        });
    });
</script>
