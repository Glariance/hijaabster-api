@extends('admin.layouts.auth')
@section('title', @env('APP_NAME') . ' | 500 Not Found')
@section('content')
    <div class="wrapper">
        <div class="error-404 d-flex align-items-center justify-content-center">
            <div class="container">
                <div class="card py-5">
                    <div class="row g-0">
                        <div class="col col-xl-5">
                            <div class="card-body p-4">
                                <h1 class="display-1"><span class="text-white">500</span></h1>
                                <h2 class="font-weight-bold display-4">Sorry, unexpected error</h2>
                                <p>Looks like you are lost!
                                    <br>May be you are not connected to the internet!
                                </p>
                                <div class="mt-5"> <a href="{{route('admin.dashboard')}}"
                                        class="btn btn-light btn-lg px-md-5 radius-30">Go Home</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <img src="{{asset('adminassets/images/error/500.png')}}"
                                class="img-fluid" alt="">
                        </div>
                    </div>
                    <!--end row-->
                </div>
            </div>
        </div>
    </div>
@endsection
