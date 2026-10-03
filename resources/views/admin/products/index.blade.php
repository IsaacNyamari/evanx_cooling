@extends('layouts.app')

@section('title', 'Products - ' . config('app.name'))

@section('content')
    <div class="col-12"><livewire:admin.product-index /></div>
@endsection
