@extends('app')

@section('title', $product->name . ' - ' . config('app.name'))
@section('meta_description', $product->summary(155) ?: $product->name)
@section('og_title', $product->name . ' - ' . config('app.name'))
@section('og_description', $product->summary(200))
@section('og_image', $product->imageUrl() ?? asset('img/og-image.jpg'))
@section('canonical', route('shop.show', $product->slug))

@section('content')
    <livewire:shop.product-detail :product="$product" />
@endsection
