@php
    $primary = '#7c3aed';
    $deep = '#2b0050';
    $text = '#1f2937';
    $logo = (config('app.url') ? rtrim(config('app.url'), '/') : '') . '/adminassets/images/logo.png';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f1fb;
            margin: 0;
            padding: 24px;
        }
        .wrapper {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
        }
        .logo {
            text-align: center;
            padding: 20px 0 10px;
            background: #ffffff;
        }
        .logo img {
            max-width: 180px;
            height: auto;
        }
        .hero {
            background: linear-gradient(135deg, {{ $deep }} 0%, {{ $primary }} 100%);
            color: #f3e8ff;
            padding: 28px 24px;
        }
        .hero h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
        }
        .content {
            padding: 24px;
            color: {{ $text }};
            line-height: 1.6;
        }
        .footer {
            padding: 16px 24px 24px;
            color: #6b7280;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="logo">
            <img src="{{ $logo }}" alt="{{ config('app.name') }} logo">
        </div>
        <div class="hero">
            <h1>{{ $subject ?? config('app.name') }}</h1>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            If you have questions, reply to this email and our team will assist.<br>
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
