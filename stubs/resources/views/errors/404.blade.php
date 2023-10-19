@extends('layouts.auth')

@section('content')
    <h1>404</h1>
    <p>{{ __('boilerplate::ui.not_found') }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">{{ __('boilerplate::ui.home') }}</a>
@endsection