@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Blogs Management')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Panel</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Blogs Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <button type="button"
                        onclick="showAjaxModal('Create New Blog', 'Save', `{{ route('admin.blogs.create') }}`)"
                        class="btn btn-light px-5">Create</button>
                </div>
            </div>
            <h6 class="mb-0 text-uppercase">Blogs Management</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <table id="blogs-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.no</th>
                                <th>Title</th>
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
                ajaxPost('#blog-form', '#blog-btn', function(response) {
                    successMessage(response.success);
                    $('#custom-lg-modal').modal('hide');
                    loadDatatable();
                });
                loadDatatable();
            });

            function loadDatatable() {
                if ($.fn.DataTable.isDataTable('#blogs-table')) {
                    $('#blogs-table').DataTable().clear().destroy();
                }

                let table = $('#blogs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.blogs.index') }}",
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
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'status',
                            name: 'status'
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
                    buttons: ['csv', 'pdf', 'print']
                });
            }

            function deleteBlogComment(id, url) {
                confirmDelete(() => {
                    handleAjaxRequest(url, function() {
                        // loadDatatable();
                        $("#comment-row-id-"+id).remove();
                    });
                });
            }
            function deleteBlogMedia(id, url) {
                confirmDelete(() => {
                    handleAjaxRequest(url, function() {
                        // loadDatatable();
                        $('#media-col-id-'+id).remove();
                    });
                });
            }
            function deleteBlog(id, url) {
                confirmDelete(() => {
                    handleAjaxRequest(url, function() {
                        loadDatatable();
                    });
                });
            }

            function updateStatus(id, url) {
                ajaxGet(url,
                function(response) {
                    $("#comment-status-btn-id-"+id).remove();
                });
            }
        </script>
    @endpush

@endsection
