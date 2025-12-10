<div class="card-body">
    <form class="row g-3" id="custom-send-mail-form" action="{{ route('admin.newsletter-management.send-mail') }}"
        method="POST">
        @csrf
        @isset($data['email'])
            <div class="col-md-12">
                <label for="emails" class="form-label">Emails</label>
                <input type="email" class="form-control" name="emails[]" id="emails"
                    value="{{ isset($data['email']) ? $data['email'] : '' }}" readonly>
                {{-- <select class="select2" name="emails[]" data-placeholder="Choose Emails to send Newsletter"
                    multiple="multiple">
                    <option value="select_all">Select All</option>
                    @foreach ($emails as $email)
                        <option value="{{ $email->email }}">{{ $email->email }}</option>
                    @endforeach
                </select> --}}
            </div>
        @endisset
        <input type="hidden" name="type" value="{{ isset($data['type']) ? $data['type'] : '' }}">
        <div class="col-md-12">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control"
                value="{{ isset($data['subject']) ? $data['subject'] : '' }}">
        </div>
        <div class="col-md-12">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" cols="30" class="form-control myEditor" rows="10"></textarea>
        </div>
        <div class="col-12">
            <button type="submit" id="custom-send-mail-btn" class="btn btn-light mt-3 px-5 btnSuccessText"></button>
        </div>
    </form>
</div>

<script>
    $('.myEditor').each(function(index) {
        var elementId = $(this).attr('id') || 'editor-' + index; // Assign a unique ID if missing
        $(this).attr('id', elementId); // Set the ID if not already present
        CKEDITOR.replace(elementId, {
            width: '100%',
            // removePlugins: ['ExportPdf', 'ExportWord', 'TrackChanges', 'Comments']
        });
    });

    // $('select').each(function() {
    //     if (!$(this).hasClass('select2-hidden-accessible')) {
    //         $(this).select2({
    //             theme: 'bootstrap4',
    //             width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ?
    //                 '100%' : 'style',
    //             placeholder: $(this).data('placeholder'),
    //             allowClear: Boolean($(this).data('allow-clear')),
    //         });
    //     }
    // });

    // // Handle "Select All" functionality
    // $('.select2').on('change', function() {
    //     let selected = $(this).val();
    //     if (selected && selected.includes('select_all')) {
    //         // Get all option values except 'select_all'
    //         let allOptions = $(this).find('option').map(function() {
    //             return this.value !== 'select_all' ? this.value : null;
    //         }).get();

    //         // Set all options as selected
    //         $(this).val(allOptions).trigger('change.select2');
    //     }
    // });
</script>
