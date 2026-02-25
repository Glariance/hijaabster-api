@extends('admin.layouts.app')

@section('title', 'Orders | ' . config('app.name'))

@section('content')
<div class="page-wrapper">
  <div class="page-content">
    <h6 class="mb-0 text-uppercase">Order Management</h6>
    <hr />
    <div class="card">
      <div class="card-body">
        <table id="orders-table" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>S.no</th>
              <th>Order #</th>
              <th>Customer</th>
              <th>Email</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S.no</th>
              <th>Order #</th>
              <th>Customer</th>
              <th>Email</th>
              <th>Total</th>
              <th>Status</th>
              <th>Date</th>
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
    loadOrdersTable();
  });

  function loadOrdersTable() {
    if ($.fn.DataTable.isDataTable('#orders-table')) {
      $('#orders-table').DataTable().clear().destroy();
    }

    $('#orders-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('admin.orders.index') }}",
      columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'tracking_number', name: 'tracking_number' },
        { data: 'customer_name', name: 'customer_name' },
        { data: 'customer_email', name: 'customer_email' },
        { data: 'total', name: 'total' },
        { data: 'status', name: 'status' },
        { data: 'created_at', name: 'created_at' },
        { data: 'action', name: 'action', orderable: false, searchable: false },
      ],
      order: [[ 7, 'desc' ]],
      dom: 'Bflrtip',
      buttons: ['csv', 'pdf', 'print']
    });
  }
</script>
@endpush
@endsection
