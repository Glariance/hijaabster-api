@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Contact Inquiry Management')
@section('content')

    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Panel</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Contact Inquiry Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    {{-- <button type="button"
                        onclick="showAjaxModal('Send Mail to Subscribers', 'Send', `{{ route('admin.newsletter-management.send-mail-view') }}`)"
                        class="btn btn-light px-5">Send Mail</button>
                    <button type="button"
                        onclick="showAjaxModal('Add Email into NewsLetter', 'Save', `{{ route('admin.newsletter-management.create') }}`)"
                        class="btn btn-light px-5">Create</button> --}}
                </div>
            </div>
            <!--end breadcrumb-->

            <h6 class="mb-0 text-uppercase">Contact Inquiry Management</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    {{-- <div class="table-responsive"> --}}
                    <table id="contact-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Received At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- <tbody>
                                @foreach ($contactInquries as $inquiry)
                                    <tr id="newsletter-row-{{ $inquiry->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $inquiry->name }}</td>
                                        <td>{{ $inquiry->email }}</td>
                                        <td>{{ $inquiry->subject }}</td>
                                        <td>{{ $inquiry->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <a href="javascript:;"
                                                onclick="showAjaxModal('Contact Inquiry Details', '', `{{ route('admin.contact-inquiry.show', $inquiry) }}`)"
                                                class="btn btn-light"><i class="lni lni-eye"></i></a>
                                            <a href="javascript:;"
                                                onclick="showAjaxModal('Update Existing Email', 'Update', `{{ route('admin.contact-inquiry.edit', $inquiry->id) }}`)"
                                                class="btn btn-light"><i class="bx bx-edit-alt"></i></a>

                                            <a href="javascript:;" class="btn btn-light"
                                                onclick="deleteNewsLetter({{ $inquiry->id }}, `{{ route('admin.contact-inquiry.destroy', [$inquiry]) }}`)"><i
                                                    class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                        <tfoot>
                            <tr>
                                <th>S.no</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Received At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // ajaxPost('#newsletter-form', '#newsletter-btn', function(response) {
                //     successMessage(response.success);
                //     $('#custom-lg-modal').modal('hide');
                //     window.location.reload();
                // });
                loadDatatable();
            });

            function loadDatatable(){

                if ($.fn.DataTable.isDataTable('#contact-table')) {
                    $('#contact-table').DataTable().clear().destroy();
                }

                let table = $('#contact-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.contact-inquiry.index') }}",
                    lengthChange: true, // 👈 Enables "per page" dropdown
                    lengthMenu: [10, 25, 50, 100, 250, 500], // 👈 Custom per page options
                    pageLength: 25, // 👈 Default per page
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'email', name: 'email' },
                        { data: 'subject', name: 'subject' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'is_read', name: 'is_read' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    dom: 'Bflrtip',
                    buttons: [
                        'csv', 'pdf', 'print'
                    ]
                });
            }

            function deleteInquiry(id, url) {
                confirmDelete(() => {
                    handleAjaxRequest(url, function() {
                        loadDatatable()
                    });
                });
            }
        </script>
        <!--app JS-->
    @endpush
@endsection
