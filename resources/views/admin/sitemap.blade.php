@extends('layouts.app')

@section('title', 'Sitemap - ' . config('app.name'))
@section('heading', 'Sitemap')

@section('content')
    <div class="col-12"><livewire:admin.sitemap-manager /></div>
@endsection
