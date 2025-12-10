{{-- <div class="card-body"> --}}
<div class="card-title d-flex align-items-center justify-content-between">
    <h5 class="mb-0 text-white">Manage {{ $section->section_name }} Group Fields</h5>
    @if ($section->repeaterGroups->isNotEmpty())
        <div>
            <button type="button" class="btn btn-sm btn-light"
                onclick="showAjaxModal('Create New Field in Group', 'Create', `{{ route('admin.cms.section.addFieldsInGroup', ['sectionId' => $section->id]) }}`)">
                <i class="bx bx-plus"></i> Add Field
            </button>
            <button type="button" class="btn btn-sm btn-light" id="btnCopyGroup">
                <i class="bx bx-copy"></i> Copy Group
            </button>
        </div>
    @endif
</div>
<hr>
<form class="g-3" id="cms-section-field-form"
    action="{{ route('admin.cms.section.group.store', ['sectionId' => $section->id]) }}" method="post">
    @csrf
    @if ($section->fields->where('field_group', null)->isNotEmpty())
        <div class="row">
            @foreach ($section->fields->where('field_group', null) as $field)
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
    @endif
    @if ($section->repeaterGroups->isNotEmpty())
        <div class="accordion" id="accordionPanelsStayOpenExample">
            @foreach ($section->repeaterGroups as $index => $group)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-heading{{ $index }}">
                        <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse{{ $index }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                            aria-controls="panelsStayOpen-collapse{{ $index }}">
                            {{ ucfirst($group->field_group) }}
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapse{{ $index }}"
                        class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                        aria-labelledby="panelsStayOpen-heading{{ $index }}">
                        <div class="accordion-body">
                            <div class="row">
                                @foreach ($section->fields->where('field_group', $group->field_group) as $field)
                                    <div class="col-md-{{ $field->field_type === 'textarea' ? '12' : '6' }}">
                                        <div class="form-group setting-input-group">
                                            <div class="d-flex justify-content-between">
                                                <label>{{ ucfirst($field->field_name) }}
                                                    ({{ $field->field_type }})
                                                </label>
                                                @if ($field->field_type === 'textarea')
                                                    <a href="javascript:;"
                                                        onclick="deleteField({{ $field->id }}, `{{ route('admin.cms.section.field.delete', ['id' => $field->id]) }}`)"
                                                        class="text-light delete-btn">(Delete)</a>
                                                @endif
                                            </div>
                                            <div class="input-group ">
                                                @if ($field->field_type === 'text')
                                                    <input type="text" name="fields[{{ $field->id }}]"
                                                        class="form-control" value="{{ $field->field_value }}">
                                                @elseif($field->field_type === 'textarea')
                                                    <textarea name="fields[{{ $field->id }}]" class="form-control myEditor">{{ $field->field_value }}</textarea>
                                                @elseif($field->field_type === 'image')
                                                    <input type="file" name="fields[{{ $field->id }}]"
                                                        class="form-control">
                                                    @if ($field->field_value)
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-light view-image-btn"
                                                            onclick="openImageModal('{{ asset('storage/' . $field->field_value) }}')">
                                                            <i class="bx bx-image"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if ($field->field_type != 'textarea')
                                                    <button class="btn btn-light delete-btn" type="button"
                                                        onclick="deleteField({{ $field->id }}, `{{ route('admin.cms.section.field.delete', ['id' => $field->id]) }}`)">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center">
            <p>No groups found.</p>
            <button type="button" class="btn btn-light px-5" onclick="addNewGroup()">Add Group</button>
        </div>
    @endif


    <div id="newGroups"></div>

    <div class="col-12">
        <button type="submit" id="btnSectionField" class="btn btn-light px-5 mt-5">Save Fields</button>
    </div>
</form>
{{-- </div> --}}

<script>
    function addNewGroup() {
        let uniqueId = Date.now();
        let newGroup = `
            <div class="col-12 new-group mt-3">
                <div class="group-header d-flex align-items-center bg-light p-2">
                    <input type="text" name="new_groups[${uniqueId}]" class="form-control mr-2 w-50"
                        placeholder="Enter group name" value="Group_1" readonly>
                        <button type="button" class="btn btn-sm btn-light mt-1" onclick="addNewField(${uniqueId})">
                            <i class="bx bx-plus"></i> Add Fields
                        </button>
                </div>
                <div class="group-fields" id="group-fields-${uniqueId}"></div>
            </div>
        `;
        document.getElementById('newGroups').insertAdjacentHTML('beforeend', newGroup);
    }

    function addNewField(groupId) {
        let uniqueId = Date.now();
        let newField = `
            <div class="form-group new-setting">
                <div class="row">
                    <div class="col-md-5">
                        <label>Field Name</label>
                        <input type="text" name="new_fields[${groupId}][${uniqueId}][name]" class="form-control" placeholder="Enter field name">
                    </div>
                    <div class="col-md-5">
                        <label>Field Type</label>
                        <select name="new_fields[${groupId}][${uniqueId}][type]" class="form-select">
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
        document.getElementById(`group-fields-${groupId}`).insertAdjacentHTML('beforeend', newField);
    }

    function removeField(button) {
        button.closest('.new-setting').remove();
    }
    $(function() {
        $('#btnCopyGroup').click(function() {
            // alert('btnCopyGroup');
            ajaxGet("{{ route('admin.cms.section.group.copy', ['sectionId' => $section->id]) }}",
                function(response) {
                    loadPage(
                        "{{ route('admin.cms.section.fields', ['sectionId' => $section->id]) }}",
                        '#load-field-page'); // Load the page dynamically
                    // $("#load-field-page").html(response); // Load the response into an element
                });
        });
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
