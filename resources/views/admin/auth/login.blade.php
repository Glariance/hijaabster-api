@extends('admin.layouts.auth')
@section('title', @env('APP_NAME') . ' | Admin Login')
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
                                        <form id="loginForm" class="row g-3" action="{{ route('admin.adminLogin') }}"
                                            method="post">
                                            @csrf
                                            <div class="text-center">
                                                <img src="{{ asset(getSetting('site logo', 'adminassets/images/pureserenity-logo.png')) }}" width="200"
                                                    alt="" />
                                            </div>
                                            <div class="col-12">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" value="{{ old('email') }}"
                                                    id="email" name="email" placeholder="Email Address">
                                            </div>
                                            <div class="col-12">
                                                <label for="inputChoosePassword" class="form-label">Enter
                                                    Password</label>
                                                <div class="input-group show_hide_password">
                                                    <input type="password" class="form-control border-end-0"
                                                        id="inputChoosePassword" value="" name="password"
                                                        placeholder="Enter Password"> <a href="javascript:;"
                                                        class="input-group-text bg-transparent"><i
                                                            class='bx bx-hide'></i></a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="remember"
                                                        id="flexSwitchCheckChecked">
                                                    <label class="form-check-label" for="flexSwitchCheckChecked">Remember
                                                        Me</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-end"> <a href="{{ route('admin.forgot') }}">Forgot
                                                    Password ?</a>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" id="loginBtn" class="btn btn-light"><i
                                                            class="bx bxs-lock-open"></i>Sign in</button>
                                                </div>
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
    {{-- window.location.href = "{{ route('admin.dashboard') }}"; --}}
    @push('scripts')
        <script>
            $(document).ready(function() {
                bindPasswordToggle();
                ajaxPost("#loginForm", "#loginBtn", function(response) {
                    successMessage(response.success);
                    window.location.href = response.redirect_to;
                }, function(xhr) {
                    // error code here
                });

            });
        </script>
    @endpush
@endsection
