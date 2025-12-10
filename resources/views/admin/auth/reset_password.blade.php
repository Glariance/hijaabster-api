@extends('admin.layouts.auth')
@section('title', @env('APP_NAME') . ' | Reset Password')
@section('content')
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                    <div class="col mx-auto">
                        <div class="mb-4 text-center">
                            {{-- <img src="{{ asset(getSetting('site logo', 'adminassets/images/pureserenity-logo.png')) }}" width="180" alt="" /> --}}
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="border p-4 rounded">
                                    <div class="form-body">
                                        <form id="reset-password-form" action="{{ route('admin.reset.password.post') }}"
                                            method="post">
                                            @csrf
                                            <div class="text-center">
                                                <img src="{{ asset(getSetting('site logo', 'adminassets/images/pureserenity-logo.png')) }}"
                                                    width="200" alt="" />
                                            </div>
                                            <h4 class="mt-5 font-weight-bold">Genrate New Password</h4>
                                            <p class="">We received your reset password request. Please enter your new
                                                password!</p>
                                            <input type="hidden" name="token" value="{{ $token }}">
                                            <input type="hidden" name="email" value="{{ $email }}">
                                            <div class="mb-3 mt-5">
                                                <label class="form-label">New Password</label>
                                                <input type="password" name="password" class="form-control"
                                                    placeholder="Enter new password" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    placeholder="Confirm password" />
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button type="submit" id="btnResetPassword" class="btn btn-light">Change
                                                    Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                ajaxPost("#reset-password-form", "#btnResetPassword", function(response) {
                    successMessage(response.success);
                    window.location.href = "{{ route('admin.login') }}";
                }, function(xhr) {
                    // error code here
                });
            });
        </script>
    @endpush
@endsection
