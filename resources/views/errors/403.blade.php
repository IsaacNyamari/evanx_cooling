@extends('errors.layout')
@section('code', '403')
@section('title', 'Access denied')
@section('message', "You don't have permission to access this page.")
@section('actions')
    <a href="{{ url('/') }}" class="btn btn-primary py-2 px-4">Back to home</a>
    @guest <a href="{{ route('login') }}" class="btn btn-outline-primary py-2 px-4">Sign in</a> @endguest
@endsection
