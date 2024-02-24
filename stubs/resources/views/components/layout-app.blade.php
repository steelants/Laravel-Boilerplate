<!doctype html>
<html data-bs-theme="{{ Cookie::get('theme') }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">

    <!-- CSRF Token -->
    <meta content="{{ csrf_token() }}" name="csrf-token">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- <link href="{{ asset('/manifest.json') }}" rel="manifest"> --}}
    <link href="{{ asset('/favicon.ico') }}" rel="shortcut icon" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Include the Quill library -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://unpkg.com/quill-table-ui@1.0.5/dist/umd/index.js" type="text/javascript"></script>
    <link href="https://unpkg.com/quill-table-ui@1.0.5/dist/index.css" rel="stylesheet">

    <!-- Scripts -->
    @livewireStyles
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    {{-- <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ asset('/service-worker.js') }}');
            });
        }
    </script> --}}

</head>

<body>
    <div id="app">
        <nav class="navbar navbar-main navbar-expand" style="z-index: 100;">
            <div class="container-fluid">
                <button aria-expanded="false" aria-label="{{ __('Toggle navigation') }}"
                    class="nav-toggler btn btn-light ml-2 d-xl-none" onclick="$('.layout-nav').toggle();"
                    type="button">
                    <i class="fas fa-bars"></i>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    <img height="32px" src="{{ asset('images/logo.png') }}" width="32px">
                </a>

                @auth
                    @include('partials.navbar-nav')
                @endauth
            </div>
        </nav>

        <div class="layout">

            @include('partials.navigation')
            @include('partials.navigation-mobile')

            <div class="layout-content">
                <div class="content">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @include('partials.alerts')
    </div>

    @livewireScripts
    @livewire('modal-basic', key('modal'))
    @stack('scripts')
</body>

</html>
