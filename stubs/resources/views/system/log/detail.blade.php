@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.file')}} - {{ $filename }}</h1>
        </div>

        <pre class="p-2 border">{!! $content !!}</pre>

        <div id="end"></div>
    </div>
@endsection