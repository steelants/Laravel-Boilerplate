<!doctype html>
<html data-bs-theme="{{ Cookie::get('theme') }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">

    <!-- CSRF Token -->
    <meta content="{{ csrf_token() }}" name="csrf-token">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('/manifest.json') }}" rel="manifest">
    <link href="{{ asset('/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <div class="row g-0 h-100">
            <div class="align-content-center col-md-6 d-none d-md-flex flex-column justify-content-center bg-body-tertiary">
                <div class="container py-4 text-center">
                    <a href="{{ url('/') }}">
                        <img class="mb-4" src="{{ asset('storage/images/logo.png') }}">
                    </a>
                    <h1>{{ config('app.name', 'Laravel') }}</h1>
                    <p class="text-black-50">
                        {{ __('general.MetaDescription') }}
                    </p>
                </div>
            </div>
            <div class="col-md-6 d-flex flex-column grid justify-content-center align-content-center">
                <div class="container py-4 px-4">
                    <div class="d-md-none text-center mb-4">
                            <img src="{{ asset('storage/images/logo.png') }}" class="mb-4" width="64px" height="64px">
                    </div>

                    <x-alerts/>

                    <div class="row justify-content-center">
                        <div class="col-md-10 col-xl-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
