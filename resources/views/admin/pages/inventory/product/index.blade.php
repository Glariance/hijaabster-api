@extends('admin.layouts.app')
@section('title', env('APP_NAME') . ' | Product Management')
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
                            <li class="breadcrumb-item active" aria-current="page">Product Management</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    {{-- <button type="button"
                        onclick="showAjaxModal('Send Mail to Subscribers', 'Send', `{{ route('admin.newsletter-management.send-mail-view') }}`)"
                        class="btn btn-light px-5">Send Mail</button> --}}
                    <a href="{{route('admin.inventory.product.create')}}"
                    class="btn btn-light px-5">Create</a>
                </div>
            </div>
            <!--end breadcrumb-->

            <h6 class="mb-0 text-uppercase">Product Management</h6>
            <hr />
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Category</label>
                            <select id="category-filter" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $parent)
                                    <option value="" disabled>— {{ $parent->name }} —</option>
                                    @foreach ($parent->children as $child)
                                        <option value="{{ $child->id }}">{{ $parent->name }} > {{ $child->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table id="product-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.no</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.no</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>Image</th>
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
                loadDatatable();
                $('#category-filter').on('change', function() {
                    loadDatatable();
                });
            });

            function loadDatatable() {

                if ($.fn.DataTable.isDataTable('#product-table')) {
                    $('#product-table').DataTable().clear().destroy();
                }

                let table = $('#product-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.inventory.product.index') }}",
                        data: function(d) {
                            d.category_id = $('#category-filter').val();
                        }
                    },
                    lengthChange: true, // 👈 Enables "per page" dropdown
                    lengthMenu: [10, 25, 50, 100, 250, 500], // 👈 Custom per page options
                    pageLength: 25, // 👈 Default per page
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'category_id',
                            name: 'category_id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'image',
                            name: 'image'
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
