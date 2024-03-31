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

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">

    <!-- Quill -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet">
    <style>
        .quill-editor-wrap {
            position: relative;
            min-height: 9rem;
            display: flex;
            flex-direction: column;
        }

        .quill-editor-wrap textarea {
            display: none;
        }

        .quill-editor {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .quill-editor .ql-editor {
            flex-grow: 1;
        }

        .quill-loading {
            position: absolute;
            z-index: 10;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            display: flex;
            border-radius: var(--bs-border-radius);
            border: var(--bs-border-width) solid var(--bs-border-color);
        }

        .quill-editor.ready+.quill-loading {
            display: none;
        }
    </style>
    <script>
        window.loadQuill = function() {
            document.querySelectorAll('.quill-editor:not(.ready)').forEach(function(element) {
                let textarea = element.closest('.quill-container').querySelector('.quill-textarea');

                let quill = new Quill(element, {
                    theme: 'snow'
                });

                quill.root.innerHTML = textarea.value;

                quill.on('text-change', function() {
                    let value = quill.root.innerHTML;
                    textarea.value = value;
                    textarea.dispatchEvent(new Event('input'));
                });

                element.classList.add('ready');
                element.closest('.quill-container').querySelector('.quill-loading').remove();
            });
        }
        window.loadQuill();
    </script>

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
                    <img height="32px" src="{{ asset('storage/images/logo.png') }}" width="32px">
                </a>

                @auth
                    @include('partials.navbar-nav')
                @endauth
            </div>
        </nav>

        <div class="layout">
            <x-navigation />
            @include('partials.navigation-mobile')

            <div class="layout-content">
                <div class="content">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <x-alerts />
    </div>

    @livewireScripts
    @livewire('modal-basic', key('modal'))
    @stack('scripts')
</body>

</html>
