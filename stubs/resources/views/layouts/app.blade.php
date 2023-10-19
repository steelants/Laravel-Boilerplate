<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  data-bs-theme="{{ Cookie::get('theme') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @livewireStyles
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-main navbar-expand" style="z-index: 100;">
            <div class="container-fluid">
                <button class="nav-toggler btn btn-light ml-2 d-xl-none" type="button" onclick="$('.layout-nav').toggle();" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <i class="fas fa-bars"></i>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" width="32px" height="32px">
                </a>

                @auth
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <label class="align-items-center d-flex dropdown-item">
                                    <span class="flex-grow-1">
                                        {{ __('web.dark_mode') }}
                                    </span>
                                    <div class="form-switch ms-4">
                                        <input {{ Cookie::get('theme') == 'dark' ? 'checked' : '' }} id="datk-theme" class="form-check-input me-0" onchange="toggleDatkTheme()" type="checkbox">
                                    </div>
                                </label>

                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    {{ __('ui.logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                @endauth
            </div>
        </nav>

        <div class="layout">
            @auth
                @include('partials.navigation')
            @endauth
            <div class="layout-content">
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>

        @include('partials.alerts')
    </div>

    @livewireScriptConfig
    @livewire('modal-basic', key('modal'))
</body>
</html>
