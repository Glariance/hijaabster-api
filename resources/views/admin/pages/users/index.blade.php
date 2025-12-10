@extends('admin.layouts.app')

@section('title', 'Users | ' . config('app.name'))

@section('content')
<div class="page-wrapper">
  <div class="page-content">
    <h6 class="mb-0 text-uppercase">User Management</h6>
    <hr />
    <div class="card">
      <div class="card-body">
        <table id="users-table" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>S.no</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S.no</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
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
    loadUsersTable();
  });

  function loadUsersTable() {
    if ($.fn.DataTable.isDataTable('#users-table')) {
      $('#users-table').DataTable().clear().destroy();
    }

    $('#users-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('admin.users.index') }}",
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'role', name: 'role' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
      ],
      dom: 'Bflrtip',
      buttons: ['csv', 'pdf', 'print']
    });
  }

  function deleteUser(id, url) {
    confirmDelete(() => {
      handleAjaxRequest(url, function() {
        loadUsersTable();
      });
    });
  }
</script>
@endpush
@endsection
