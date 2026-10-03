@extends('layouts.app')

@section('title', ($product->exists ? 'Edit' : 'New') . ' Product - ' . config('app.name'))
@section('heading', $product->exists ? 'Edit product' : 'New product')

@section('content')
    <div class="col-12"><livewire:admin.product-form :product="$product" /></div>
@endsection
