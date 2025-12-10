@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | NewsLetter Management')
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
                            <li class="breadcrumb-item active" aria-current="page">NewsLetter Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <button type="button"
                        onclick="showAjaxModal('Send Mail to Subscribers', 'Send', `{{ route('admin.newsletter-management.send-mail-view', ['type'=>'newsletter']) }}`)"
                        class="btn btn-light px-5">Send Mail</button>
                    <button type="button"
                        onclick="showAjaxModal('Add Email into NewsLetter', 'Save', `{{ route('admin.newsletter-management.create') }}`)"
                        class="btn btn-light px-5">Create</button>
                </div>
            </div>
            <!--end breadcrumb-->

            <h6 class="mb-0 text-uppercase">NewsLetter Management</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    {{-- <div class="table-responsive"> --}}
                        <table id="newsletter-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Email</th>
                                    <th>Register At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @foreach ($newsletters as $newsletter)
                                    <tr id="newsletter-row-{{ $newsletter->id }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $newsletter->email }}</td>
                                        <td>{{ $newsletter->is_subscribed ? 'Subscribed' : 'Un Subscribed' }}</td>
                                        <td class="text-center">
                                            <a href="javascript:;"
                                                onclick="showAjaxModal('Update Existing Email', 'Update', `{{ route('admin.newsletter-management.edit', $newsletter->id) }}`)"
                                                class="btn btn-light"><i class="bx bx-edit-alt"></i></a>

                                            <a href="javascript:;" class="btn btn-light"
                                                onclick="deleteNewsLetter({{ $newsletter->id }}, `{{ route('admin.newsletter-management.destroy', [$newsletter]) }}`)"><i
                                                    class="bx bx-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
                            <tfoot>
                                <tr>
                                    <th>S.no</th>
                                    <th>Email</th>
                                    <th>Register At</th>
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
                ajaxPost('#newsletter-form', '#newsletter-btn', function(response) {
                    successMessage(response.success);
                    $('#custom-lg-modal').modal('hide');
                    loadDatatable();
                    // window.location.reload();
                });
                loadDatatable();
            });

            function loadDatatable() {
                if ($.fn.DataTable.isDataTable('#newsletter-table')) {
                    $('#newsletter-table').DataTable().clear().destroy();
                }
                let table = $('#newsletter-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.newsletter-management.index') }}",
                    lengthChange: true,
                    lengthMenu: [10, 25, 50, 100, 250, 500],
                    pageLength:25,
                    columns:[
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'email', name: 'email' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'is_subscribed', name: 'is_subscribed' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    dom: 'Bflrtip',
                    buttons: [
                        'csv', 'pdf', 'print'
                    ]

                })
            }

            function deleteNewsLetter(id, url) {
                confirmDelete(() => {
                    handleAjaxRequest(url, function() {
                        loadDatatable();
                        // $('#newsletter-row-' + id).remove();
                    });
                });
            }
        </script>
        <!--app JS-->
    @endpush
@endsection
