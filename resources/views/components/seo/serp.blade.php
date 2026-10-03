@props(['title', 'description', 'url'])
@php
    $host = parse_url($url, PHP_URL_HOST);
    $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
    $crumbs = $path === '' ? '' : ' › '.str_replace('/', ' › ', $path);
@endphp
<div class="border rounded p-3 bg-white" style="max-width: 620px; font-family: Arial, sans-serif">
    <div class="text-truncate" style="color:#202124; font-size:12px">{{ $host }}<span style="color:#5f6368">{{ $crumbs }}</span></div>
    <div style="color:#1a0dab; font-size:19px; line-height:1.3; margin:2px 0 3px">{{ \Illuminate\Support\Str::limit($title, 62, '…') }}</div>
    <div style="color:#4d5156; font-size:13.5px; line-height:1.55">{{ \Illuminate\Support\Str::limit($description, 160, '…') }}</div>
</div>
