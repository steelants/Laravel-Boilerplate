<x-layout-auth>
    <h1>{{ $exception->getStatusCode() }}</h1>
    <p>{{ $exception->getMessage() }}</p>
    <a class="btn btn-solid theme-primary" href="{{ url('/') }}">{{ __('Home') }}</a>
</x-layout-auth>