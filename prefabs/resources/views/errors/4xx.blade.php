<x-layout-auth>
    <h1>{{ $exception->getStatusCode() }}</h1>
    <p>{{ $exception->getMessage() }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">{{ __('boilerplate::ui.home') }}</a>
</x-layout-auth>