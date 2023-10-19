@extends('layouts.auth')

@section('content')
    <h1>503</h1>
    <p>{{ __('boilerplate::ui.maintenance') }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">{{ __('boilerplate::ui.home') }}</a>
@endsection