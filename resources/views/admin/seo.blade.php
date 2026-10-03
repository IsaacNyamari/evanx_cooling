@extends('layouts.app')

@section('title', 'SEO - ' . config('app.name'))
@section('heading', 'SEO')

@section('content')
    <div class="col-12">
        <ul class="nav nav-pills mb-4 gap-1">
            @foreach (['overview' => ['Overview', 'fa-gauge-high', 'admin.seo'], 'pages' => ['Pages', 'fa-file-lines', 'admin.seo.pages'], 'products' => ['Products', 'fa-box', 'admin.seo.products']] as $key => [$label, $icon, $route])
                <li class="nav-item">
                    <a href="{{ route($route) }}" wire:navigate class="nav-link {{ $tab === $key ? 'active' : 'bg-white border' }}">
                        <i class="fa {{ $icon }} me-1"></i> {{ $label }}
                    </a>
                </li>
            @endforeach
            <li class="nav-item ms-auto"><a href="{{ route('admin.sitemap') }}" wire:navigate class="nav-link bg-white border"><i class="fa fa-sitemap me-1"></i> Sitemap</a></li>
        </ul>

        @if ($tab === 'overview')
            <livewire:admin.seo.overview />
        @elseif ($tab === 'pages')
            <livewire:admin.seo.pages />
        @else
            <livewire:admin.seo.products />
        @endif
    </div>
@endsection
