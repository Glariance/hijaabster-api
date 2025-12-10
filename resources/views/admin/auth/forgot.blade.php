@extends('admin.layouts.auth')
@section('title', @env('APP_NAME') . ' | Forgot Password')
@section('content')
    <div class="wrapper">
        <div class="authentication-forgot d-flex align-items-center justify-content-center">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                    <div class="col mx-auto">
                        {{-- <div class="mb-4 text-center">
                            <img src="" width="180" alt="" />
                        </div> --}}
                        <div class="card forgot-box">
                            <div class="card-body">
                                <form class="" id="forgotForm" action="{{ route('admin.forgotpost') }}" method="post">
                                    @csrf
                                    <div class="p-4 rounded  border">
                                        <div class="text-center">
                                            <img src="{{ asset(getSetting('site logo', 'adminassets/images/pureserenity-logo.png')) }}" width="200"
                                                alt="" />
                                        </div>
                                        <h4 class="mt-5 font-weight-bold">Forgot Password?</h4>
                                        <p class="">Enter your registered email ID to reset the password</p>
                                        <div class="my-4">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                class="form-control form-control-lg" placeholder="example@user.com" />
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" id="btnForgot" class="btn btn-light btn-lg">Send Mail</button> <a
                                                href="{{ route('admin.login') }}" class="btn btn-light btn-lg"><i
                                                    class='bx bx-arrow-back me-1'></i>Back to Login</a>
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
    @push('scripts')
        <script>
            $(document).ready(function() {
                ajaxPost("#forgotForm","#btnForgot", function(response){
                    $("input[name='email']").val('');
                    successMessage(response.success);
                    window.location.href = "{{ route('admin.login') }}";
                }, function(xhr){
                    // error code here
                });
            });

        </script>
        
    @endpush
@endsection
