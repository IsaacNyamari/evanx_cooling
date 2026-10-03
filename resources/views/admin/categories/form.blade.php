@extends('layouts.app')

@section('title', ($category->exists ? 'Edit' : 'New') . ' Category - ' . config('app.name'))
@section('heading', $category->exists ? 'Edit category' : 'New category')

@section('content')
    <div class="col-lg-8"><livewire:admin.category-form :category="$category" /></div>
@endsection
