@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Category Management')
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
                            <li class="breadcrumb-item active" aria-current="page">Category Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    {{-- <button type="button"
                        onclick="showAjaxModal('Send Mail to Subscribers', 'Send', `{{ route('admin.newsletter-management.send-mail-view') }}`)"
                        class="btn btn-light px-5">Send Mail</button> --}}
                    <button type="button"
                        onclick="showAjaxModal('Create New Category', 'Save', `{{ route('admin.inventory.category.create') }}`)"
                        class="btn btn-light px-5">Create</button>
                </div>
            </div>
            <!--end breadcrumb-->

            <h6 class="mb-0 text-uppercase">Category Management</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <table id="category-Table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Parent</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.no</th>
                                <th>Parent</th>
                                <th>Name</th>
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
                ajaxPost('#category-form', '#category-btn', function(response) {
                    successMessage(response.success);
                    $('#custom-lg-modal').modal('hide');
                    loadDatatable();
                });
                loadDatatable();
            });

            function loadDatatable() {

                if ($.fn.DataTable.isDataTable('#category-Table')) {
                    $('#category-Table').DataTable().clear().destroy();
                }

                let table = $('#category-Table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.inventory.category.index') }}",
                    lengthChange: true, // 👈 Enables "per page" dropdown
                    lengthMenu: [10, 25, 50, 100, 250, 500], // 👈 Custom per page options
                    pageLength: 25, // 👈 Default per page
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'parent_id',
                            name: 'parent_id'
                        },
                        {
                            data: 'name',
                            name: 'name'
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
