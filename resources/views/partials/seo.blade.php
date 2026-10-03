@php($seo = app(\App\Support\Seo::class)->current())
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="author" content="{{ $seo['site_name'] }}">
<meta name="robots" content="{{ $seo['robots'] }}">
<link rel="canonical" href="{{ $seo['url'] }}">

<!-- Open Graph (Facebook, WhatsApp, LinkedIn...) -->
<meta property="og:site_name" content="{{ $seo['site_name'] }}">
<meta property="og:locale" content="en_KE">
<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seo['url'] }}">
<meta property="og:image" content="{{ $seo['image'] }}">
<meta property="og:image:alt" content="{{ $seo['title'] }}">
@if ($seo['image_width'])
    <meta property="og:image:width" content="{{ $seo['image_width'] }}">
    <meta property="og:image:height" content="{{ $seo['image_height'] }}">
@endif

<!-- Twitter / X -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">

@if ($seo['json_ld'])
    <script type="application/ld+json">{!! json_encode($seo['json_ld'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endif
