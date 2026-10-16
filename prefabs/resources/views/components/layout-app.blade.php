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


    <!-- Scripts -->
    @livewireStyles
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
	@stack('styles')
	
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
        @auth
            @include('partials.navbar')
        @endauth

        <div class="layout">
            <x-navigation />

            @if (isset($subNavigation) && $subNavigation->isNotEmpty())
                <div class="layout-subnav" id="layout-subnav">
                    <div class="sidebar">
                        <div class="sidebar-content">{{ $subNavigation }}</div>
                    </div>
                </div>
            @endif

            @include('partials.navigation-mobile')

            @if($withoutWrapper ?? false)
                {{ $slot }}
            @else
                <div class="layout-content">
                    <div class="content">
                        {{ $slot }}
                    </div>
                </div>
            @endif

            @if (isset($sidebar) && $sidebar->isNotEmpty())
                <div class="layout-sidebar" id="layout-sidebar">
                    {{ $sidebar }}
                </div>
            @endif
        </div>
    </div>

    <x-alerts />

    @livewireScripts
    @livewire('modal-basic', key('modal'))
    @stack('scripts')
</body>

</html>
