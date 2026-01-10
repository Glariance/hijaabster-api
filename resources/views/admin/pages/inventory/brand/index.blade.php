@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Brands Management')
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
                            <li class="breadcrumb-item active" aria-current="page">Brands Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    {{-- <button type="button"
                        onclick="showAjaxModal('Send Mail to Subscribers', 'Send', `{{ route('admin.newsletter-management.send-mail-view') }}`)"
                        class="btn btn-light px-5">Send Mail</button> --}}
                    <button type="button"
                        onclick="showAjaxModal('Create New Tag', 'Save', `{{ route('admin.inventory.brand.create') }}`)"
                        class="btn btn-light px-5">Create</button>
                </div>
            </div>
            <h6 class="mb-0 text-uppercase">Brands Management</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <table id="brands-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.no</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                ajaxPost('#brand-form', '#brand-btn', function(response) {
                    successMessage(response.success);
                    $('#custom-lg-modal').modal('hide');
                    loadDatatable();
                });
                loadDatatable();
            });

            function loadDatatable() {

                if ($.fn.DataTable.isDataTable('#brands-table')) {
                    $('#brands-table').DataTable().clear().destroy();
                }

                let table = $('#brands-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.inventory.brand.index') }}",
                    lengthChange: true,
                    lengthMenu: [10, 25, 50, 100, 250, 500],
                    pageLength: 25,
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'image',
                            name: 'image',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    dom: 'Bflrtip',
                    buttons: [
                        'csv', 'pdf', 'print'
                    ]
                });
            }

            function deleteTag(id, url) {
                confirmDelete(() => {
                    handleAjaxRequest(url, function() {
                        loadDatatable()
                    });
                });
            }
        </script>
    @endpush
@endsection
