@extends('admin.layouts.app')

@section('title', 'User Details | ' . config('app.name'))

@section('content')
<div class="page-wrapper">
  <div class="page-content">
    <div class="d-flex align-items-center mb-3">
      <h3 class="mb-0">User Details</h3>
    </div>

    <div class="card">
      <div class="card-body">
        <table class="table">
          <tbody>
            <tr>
              <th>Name</th>
              <td>{{ $user->name }}</td>
            </tr>
            <tr>
              <th>Email</th>
              <td>{{ $user->email }}</td>
            </tr>
            <tr>
              <th>Role</th>
              <td>{{ $user->role->name ?? 'User' }}</td>
            </tr>
            <tr>
              <th>Joined</th>
              <td>{{ $user->created_at?->format('Y-m-d') }}</td>
            </tr>
          </tbody>
        </table>
        <a href="{{ route('admin.users.index') }}" class="btn btn-light mt-3">Back to list</a>
      </div>
    </div>
  </div>
</div>
@endsection
