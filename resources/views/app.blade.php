<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name') . ' - Professional HVAC Services in Nairobi')</title>
    <meta name="description" content="@yield('meta_description', 'Leading HVAC services provider in Nairobi offering AC installation, cooling repair, heating solutions, and maintenance. Certified technicians, 24/7 support.')">
    <meta name="keywords" content="@yield('meta_keywords', 'HVAC Nairobi, AC installation, cooling repair, heating services, air conditioner maintenance')">
    <meta name="author" content="@yield('meta_author', config('app.name'))">
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'Professional HVAC services in Nairobi')">
    <meta property="og:image" content="@yield('og_image', asset('img/icon/faviconV2.png'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:type" content="website">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="@yield('twitter_card', 'summary')">
    <meta name="twitter:title" content="@yield('og_title', config('app.name'))">
    <meta name="twitter:description" content="@yield('og_description', 'Professional HVAC services in Nairobi')">
    <meta name="twitter:image" content="@yield('og_image', asset('img/icon/faviconV2.png'))">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/icon/faviconV2.png') }}">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;600;800&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    {{-- vitte to be loaded --}}
    @vite(['resources/js/app.js'])
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @livewireStyles

</head>

<body>
    @include('sweetalert2::index')
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    <!-- Topbar Start -->
    <x-main-top-bar />
    <!-- Topbar End -->


    <!-- Navbar Start -->
    <x-main-navigation />
    <!-- Navbar End -->


    <main>
        @if (session('error'))
            <div class="alert alert-danger text-center mb-0 rounded-0">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    @livewireScripts
    <!-- Footer Start -->
    <x-main-footer />

    
</body>

</html>
