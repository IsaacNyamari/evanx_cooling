@extends('layouts.app')

@section('title', 'Deployments - ' . config('app.name'))
@section('heading', 'Deployments')

@section('content')
    <div class="col-12"><livewire:admin.deployments /></div>
@endsection
