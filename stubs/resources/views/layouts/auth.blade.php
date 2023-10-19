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
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <body>
        <div id="app">
           <div class="row g-0 h-100">

              <div class="align-content-center col-md-6 d-none d-md-flex flex-column justify-content-center bg-body-tertiary">
                 <div class="container py-4 text-center">
                    @guest
                       <h1>{{ config('app.name', 'Laravel') }}</h1>
                    @endguest
                    <p class="text-black-50">
                       {{ __('general.MetaDescription') }}
                    </p>
                 </div>
              </div>

              <div class="col-md-6 d-flex flex-column grid justify-content-center align-content-center">
                 <div class="container py-4 px-4">
                    <div class="d-md-none text-center mb-4">
                       <img src="{{ asset('images/logo.png') }}" class="mb-4" width="64px" height="64px">
                    </div>

                    @if (session()->has('message'))
                       <div class="alert alert-info alert-dismissible">{{ session('message') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    @endif

                    @yield('content')
                 </div>
              </div>
           </div>
        </div>
     </body>
</body>
</html>
