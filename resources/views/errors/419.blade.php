@extends('errors.layout')
@section('code', '419')
@section('title', 'Page expired')
@section('message', "Your session has expired. Please go back, refresh the page and try again.")
@section('actions')
    <a href="javascript:history.back()" class="btn btn-primary py-2 px-4">Go back</a>
    <a href="{{ url('/') }}" class="btn btn-outline-primary py-2 px-4">Home</a>
@endsection
