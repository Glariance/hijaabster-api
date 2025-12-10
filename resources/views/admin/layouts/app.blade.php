<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--favicon-->
    <link rel="icon" href="{{ asset(getSetting('site fav icon', 'adminassets/images/favicon.png')) }}"
        type="image/png" />

    @include('admin.partials.styles')

    <title>@yield('title')</title>
</head>

<body class="bg-theme bg-theme5">
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        @include('admin.partials.sidebar')
        <!--end sidebar wrapper -->
        <!--start header -->
        @include('admin.partials.header')
        <!--end header -->
        <!--start page wrapper -->
        @yield('content')
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        @include('admin.partials.footer')
    </div>

    <!--end wrapper-->
    @include('admin.partials.modal')
    <!--start switcher-->
    @include('admin.partials.theme')
    <!--end switcher-->
    @include('admin.partials.scripts')

    @include('admin.partials.alert-msg')
    <script>
        //  $(document).ready(function() {
        //     pos5_success_noti();
        // });
    </script>
    @yield('scripts')
    @stack('scripts')

</body>

</html>
