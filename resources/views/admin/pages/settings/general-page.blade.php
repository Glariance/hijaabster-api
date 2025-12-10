<div class="card-body">
    <div class="card-title d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-white">General Settings</h5>
        <button type="button" class="btn btn-light px-5 radius-30" onclick="addNewField()"><i
                class="bx bx-plus mr-1"></i>New Setting</button>
    </div>
    <hr>
    <form class="row g-3" id="general-setting-form" method="POST" action="{{ route('admin.general.post') }}"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if ($settings->isNotEmpty())
            <div class="row">
                @foreach ($settings as $setting)
                    <div class="col-md-{{ $setting->type === 'textarea' ? '6' : '6' }}">
                        <div class="form-group">
                            <label>{{ ucfirst(str_replace('_', ' ', $setting->key)) }}</label>

                            <div class="input-group mb-3 setting-input-group">
                                @if ($setting->type === 'text')
                                    <input type="text" name="settings[{{ $setting->key }}]" class="form-control"
                                        value="{{ $setting->value }}">
                                @elseif($setting->type === 'textarea')
                                    <textarea name="settings[{{ $setting->key }}]" id="editor{{ $setting->id }}" class="form-control myEditor">{{ $setting->value }}</textarea>
                                @elseif($setting->type === 'file')
                                    <input type="file" name="settings[{{ $setting->key }}]" class="form-control">
                                    @if ($setting->value)
                                        <a href="javascript:void(0);" class="btn btn-light view-image-btn"
                                            onclick="openImageModal('{{ asset('storage/' . $setting->value) }}')">
                                            <i class="bx bx-image"></i>
                                        </a>
                                    @endif
                                @endif
                                <button class="btn btn-light delete-btn" type="button"
                                    onclick="deleteSetting({{ $setting->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center" style="font-size: 24px;">
                <p>No settings found. Add new settings below.</p>
            </div>
        @endif

        <div id="newFields"></div>


        <div class="col-12">
            <button type="submit" id="btnGeneralSetting"
                class="btn btn-light px-5">{{ isset($setting) ? 'Update' : 'Save' }}</button>
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
                        <label>New Setting Key</label>
                        <input type="text" name="new_settings[${uniqueId}][key]" class="form-control" placeholder="Enter setting key">
                    </div>
                    <div class="col-md-5">
                        <label>Field Type</label>
                        <select name="new_settings[${uniqueId}][type]" class="form-select">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="file">File</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light mt-4" onclick="removeField(this)"><i class="bx bx-trash me-0"></i></button>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('newFields').insertAdjacentHTML('beforeend', newField);
    }

    function removeField(button) {
        button.closest('.new-setting').remove();
    }

    function deleteSetting(settingId) {
        const mainUri = "{{ url('/') }}";
        const url = `${mainUri}/admin/settings/general/page/${settingId}`;
        confirmDelete(() => {
            handleAjaxRequest(url);
            // loadTabContent("$general-settings");
            setTimeout(() => {
                loadTabContent("#general-settings")
            }, 2000);
        });
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
        // ClassicEditor
        //     .create(document.querySelector('.myEditor'))
        //     .catch(error => console.error(error));
    });
</script>
