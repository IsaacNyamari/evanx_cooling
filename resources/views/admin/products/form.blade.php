@extends('layouts.app')

@section('title', ($product->exists ? 'Edit' : 'New') . ' Product - ' . config('app.name'))

@section('content')
    <div class="col-12"><livewire:admin.product-form :product="$product" /></div>
@endsection
