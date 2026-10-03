<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('code') @yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/icon/faviconV2.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@600;800&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        .error-code { font-family: 'Roboto Slab', serif; font-size: clamp(6rem, 22vw, 12rem); font-weight: 800; line-height: 1; color: var(--primary); }
        .error-wrap { min-height: 100vh; }
    </style>
</head>
<body>
    <div class="error-wrap d-flex align-items-center justify-content-center bg-light px-3">
        <div class="text-center" style="max-width: 560px">
            <a href="{{ url('/') }}" class="text-decoration-none"><h5 class="text-secondary mb-4">{{ config('app.name') }}</h5></a>
            <div class="error-code">@yield('code')</div>
            <h1 class="h2 mb-3">@yield('title')</h1>
            <p class="text-muted mb-4">@yield('message')</p>
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                @hasSection('actions')
                    @yield('actions')
                @else
                    <a href="{{ url('/') }}" class="btn btn-primary py-2 px-4"><i class="fa fa-home me-2"></i>Back to home</a>
                    <a href="{{ url('/shop') }}" class="btn btn-outline-primary py-2 px-4"><i class="fa fa-store me-2"></i>Visit the shop</a>
                @endif
            </div>
            <p class="small text-muted mt-5 mb-0">Need help? Call {{ config('site.phone') }} or email
                <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></p>
        </div>
    </div>
</body>
</html>
