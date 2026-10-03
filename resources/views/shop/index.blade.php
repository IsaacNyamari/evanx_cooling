@extends('app')

@section('title', 'Shop HVAC & Refrigeration Parts - ' . config('app.name'))
@section('meta_description', 'Shop air conditioners, compressors, refrigeration parts and HVAC tools from ' . config('app.name') . ' in Nairobi, Kenya. Request a quote today.')
@section('canonical', url('/shop'))

@section('content')
    <livewire:shop.product-list />
@endsection
