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
    @livewireStyles
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-main navbar-expand" style="z-index: 100;">
            <div class="container-fluid">
                <button aria-expanded="false" aria-label="{{ __('Toggle navigation') }}" class="nav-toggler btn btn-light ml-2 d-xl-none" onclick="$('.layout-nav').toggle();" type="button">
                    <i class="fas fa-bars"></i>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    <img height="32px" src="{{ asset('images/logo.png') }}" width="32px">
                </a>

                @auth
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" id="navbarDropdown" role="button" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-end">
                                <label class="align-items-center d-flex dropdown-item">
                                    <span class="flex-grow-1">
                                        {{ __('boilerplate::ui.dark_mode') }}
                                    </span>
                                    <div class="form-switch ms-4">
                                        <input {{ Cookie::get('theme') == 'dark' ? 'checked' : '' }} class="form-check-input me-0" id="datk-theme" onchange="toggleDatkTheme()" type="checkbox">
                                    </div>
                                </label>

                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    {{ __('boilerplate::ui.profile') }}
                                </a>

                                <a class="dropdown-item" href="{{ route('profile.api') }}">
                                    {{ __('boilerplate::ui.api_tokens') }}
                                </a>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    {{ __('boilerplate::ui.logout') }}
                                </a>

                                <form action="{{ route('logout') }}" class="d-none" id="logout-form" method="POST">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                @endauth
            </div>
        </nav>

        <div class="layout">
            @include('partials.navigation')
            <div class="layout-content">
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>

        @include('partials.alerts')
    </div>

    @livewireScripts
    @livewire('modal-basic', key('modal'))
</body>

</html>
