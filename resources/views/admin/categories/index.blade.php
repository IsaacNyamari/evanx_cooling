@extends('layouts.app')

@section('title', 'Categories - ' . config('app.name'))

@section('content')
    <div class="col-12"><livewire:admin.category-index /></div>
@endsection
