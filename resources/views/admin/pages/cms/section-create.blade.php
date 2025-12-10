<div class="card-body">
    <form class="row g-3" id="cms-section-form" action="{{ route('admin.cms.section.post') }}" method="POST">
        @csrf
        <div class="col-md-4">
            <label for="section_name" class="form-label">Section Name</label>
            <input type="hidden" name="cms_page_id" value="{{ isset($section) ? $section->cms_page_id : $page->id }}">
            <input type="hidden" name="id" value="{{ isset($section) ? $section->id : '' }}">
            <input type="text" class="form-control" id="section_name" name="section_name"
                value="{{ isset($section) ? $section->section_name : '' }}">
        </div>
        <div class="col-md-4">
            <label for="section_type" class="form-label">Section Type</label>
            @if (isset($section))
                <input type="text" class="form-control" id="section_type" name="section_type"
                    value="{{ isset($section) ? $section->section_type : '' }}" readonly>
            @else
                <select name="section_type" id="section_type" class="form-select">
                    <option value="single">Single</option>
                    <option value="repeater">Repeater</option>
                </select>
            @endif
        </div>
        <div class="col-md-4">
            <label for="section_sort_order" class="form-label">Sort Order</label>
            <input type="number" name="section_sort_order" class="form-control"
                value="{{ isset($section) ? $section->section_sort_order : '0' }}">
        </div>
        <div class="col-12">
            <button type="submit" id="cms-section-btn"
                class="btn btn-light mt-5 px-5">{{ isset($section) ? 'Update' : 'Save' }}</button>
        </div>
    </form>
</div>
