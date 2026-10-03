@props(['title', 'description', 'image', 'url'])
<div class="border rounded overflow-hidden bg-white" style="max-width: 520px; font-family: Arial, sans-serif">
    <div class="bg-light" style="aspect-ratio: 1.91 / 1; overflow: hidden">
        <img src="{{ $image }}" alt="" style="width: 100%; height: 100%; object-fit: cover" loading="lazy">
    </div>
    <div class="p-3" style="background:#f2f3f5">
        <div class="text-uppercase text-truncate" style="font-size:11px; color:#65676b">{{ parse_url($url, PHP_URL_HOST) }}</div>
        <div class="fw-semibold" style="font-size:16px; line-height:1.25; color:#050505">{{ \Illuminate\Support\Str::limit($title, 70, '…') }}</div>
        <div style="font-size:13px; color:#65676b; line-height:1.35">{{ \Illuminate\Support\Str::limit($description, 110, '…') }}</div>
    </div>
</div>
