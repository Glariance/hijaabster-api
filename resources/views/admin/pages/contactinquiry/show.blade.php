<div class="card-body">
    <div class="mb-3 text-end">
        <a href="javascript:;"
            onclick="showAjaxModal('Contact Inquiry Response', 'Send', '{{ route('admin.newsletter-management.send-mail-view', ['email' => $contactInquiry->email, 'subject' => $contactInquiry->subject, 'type' => 'contact']) }}')"
            class="btn btn-light">
            <i class="lni lni-envelope"></i>
            Send Mail
        </a>
    </div>
    <table class="table table-bordered">
        <tr>
            <th class="w-25">Name</th>
            <td class="w-75">{{ $contactInquiry->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $contactInquiry->email }}</td>
        </tr>
        <tr>
            <th>Subject</th>
            <td>{{ $contactInquiry->subject }}</td>
        </tr>
        {{-- <tr>
            <th>Status</th>
            <td>
                @if ($contactInquiry->is_read)
                <span class="badge bg-success">Read</span>
                @else
                <span class="badge bg-warning">Unread</span>
                @endif
            </td>
        </tr> --}}
        <tr>
            <th>Received-At</th>
            <td>{{ $contactInquiry->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <th>Message</th>
            <td>{{ $contactInquiry->message }}</td>
        </tr>
    </table>

</div>
