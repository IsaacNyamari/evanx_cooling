@extends('layouts.app')

@section('title', ($category->exists ? 'Edit' : 'New') . ' Category - ' . config('app.name'))

@section('content')
    <div class="col-lg-8"><livewire:admin.category-form :category="$category" /></div>
@endsection
