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
                                    <input type="file" name="fields[{{ $field->id }}]" class="form-control">
                                    @if ($field->field_value)
                                        <a href="javascript:void(0);" class="btn btn-light view-image-btn"
                                            onclick="openImageModal(`{{ asset('storage/' . $field->field_value) }}`)">
                                            <i class="bx bx-image"></i>
                                        </a>
                                    @endif
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
    });
</script>
