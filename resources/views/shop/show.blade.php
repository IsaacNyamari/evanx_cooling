@extends('app')

@section('title', $product->name . ' - ' . config('app.name'))
@section('meta_description', Str::limit(trim(strip_tags($product->short_description ?? $product->name)), 155))
@section('og_title', $product->name . ' - ' . config('app.name'))
@section('og_image', $product->imageUrl() ?? asset('img/og-image.jpg'))
@section('canonical', route('shop.show', $product->slug))

@section('content')
    <livewire:shop.product-detail :product="$product" />
@endsection
