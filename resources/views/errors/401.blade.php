@extends('errors.layout')
@section('code', '401')
@section('title', 'Please sign in')
@section('message', "You need to be signed in to view this page.")
@section('actions')
    <a href="{{ route('login') }}" class="btn btn-primary py-2 px-4">Sign in</a>
    <a href="{{ url('/') }}" class="btn btn-outline-primary py-2 px-4">Back to home</a>
@endsection
