@props(['text' => '', 'kind' => 'title'])
@php
    $rating = $kind === 'title' ? \App\Support\SeoAnalyzer::title($text) : \App\Support\SeoAnalyzer::description($text);
    $max = $kind === 'title' ? \App\Support\SeoAnalyzer::TITLE_MAX : \App\Support\SeoAnalyzer::DESC_MAX;
    $color = ['good' => 'success', 'warn' => 'warning', 'bad' => 'danger'][$rating['status']];
    $pct = min(100, (int) round($rating['length'] / $max * 100));
@endphp
<div class="mt-1">
    <div class="progress" style="height: 5px"><div class="progress-bar bg-{{ $color }}" style="width: {{ $pct }}%"></div></div>
    <div class="small text-{{ $color === 'warning' ? 'warning-emphasis' : $color }}">{{ $rating['length'] }}/{{ $max }} &middot; {{ $rating['message'] }}</div>
</div>
