<div class="card-body">
    <form class="row g-3" id="cms-section-group-field-form"
        action="{{ route('admin.cms.section.group.field.post', ['sectionId' => $section->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="cms_page_section_id" value="{{ isset($section) ? $section->id : 0 }}">
        <div class="col-md-4">
            <label for="field_group" class="form-label">Section Name</label>
            <select name="field_group" id="field_group" class="form-select">
                <option value="All">All</option>
                <option value="public">Without Group</option>
                @foreach ($section->repeaterGroups as $group)
                    <option value="{{ $group->field_group }}">{{ $group->field_group }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="field_name" class="form-label">Field Name</label>
            <input type="text" name="field_name" class="form-control" value="">
        </div>
        <div class="col-md-4">
            <label for="field_type" class="form-label">Sort Order</label>
            <select name="field_type" class="form-select">
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="image">Image</option>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" id="cms-section-group-field-btn" class="btn btn-light mt-5 px-5">Save</button>
        </div>
    </form>
</div>
