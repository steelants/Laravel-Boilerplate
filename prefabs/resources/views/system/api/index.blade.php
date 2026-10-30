<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('Api') }}</h1>
            <div class="d-flex gap-2">
                <a class="btn btn-outline theme-secondary" href="{{ route('system.api.export', 'openapi') }}"><i class="fa-solid fa-download me-1"></i>OpenAPI</a>
                <a class="btn btn-outline theme-secondary" href="{{ route('system.api.export', 'insomnia') }}"><i class="fa-solid fa-download me-1"></i>Insomnia</a>
                <a class="btn btn-outline theme-secondary" href="{{ route('system.api.export', 'restfox') }}"><i class="fa-solid fa-download me-1"></i>Restfox</a>
            </div>
        </div>
        <div>
            @foreach ($routes as $route)
                @php
                    // stable id, the open / closed state is remembered (toggleState cookie)
                    $collapseId = 'api-' . \Illuminate\Support\Str::slug($route['Method'] . ' ' . str_replace(['/', '{', '}', '?'], ' ', $route['Uri']));
                    $isOpen = getToggleState($collapseId) == 'open';
                    $color = 'info';
                    switch ($route['Method']) {
                        case 'GET':
                            $color = 'info';
                            break;
                        case 'POST':
                            $color = 'success';
                            break;
                        case 'PUT':
                            $color = 'warning';
                            break;
                        case 'DELETE':
                            $color = 'danger';
                            break;
                    }
                @endphp

                <div class="mb-3">
                    <div class="btn-toggle btn py-3 bg-subtle-{{ $color }} w-100 text-start position-relative">
                        <b class="badge theme-{{ $color }} me-2">{{ $route['Method'] }}</b>
                        <b class="position-relative z-2"
                            onclick="copyToClipboard('{{ url($route['Uri']) }}');"
                            title="{{ __('Copy to clipboard') }}"
                        >
                            <span>{{ $route['Uri'] }}</span>
                            <i class="fa-solid fa-clipboard"></i>
                        </b>
                        @if ($route['RequiresToken'])
                            <span class="badge bg-secondary fg-contrast-secondary ms-2" title="{{ __('Requires an API token') }}"><i class="fa-solid fa-lock"></i></span>
                        @endif
                        <a class="stretched-link" data-bs-target="#{{ $collapseId }}" data-bs-toggle="collapse" aria-expanded="{{ $isOpen ? 'true' : 'false' }}"></a>
                    </div>
                    <div class="remember collapse {{ $isOpen ? 'show' : '' }}" id="{{ $collapseId }}">
                        <div class="card card-body mt-2">
                            <h6>{{ __('Description') }}</h6>
                            <pre>{{ $route['Description'] }}</pre>

                            @if ($route['Parameters'])
                                <h6>{{ __('Parameters') }}</h6>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Rules / type') }}</th>
                                        <th>{{ __('Comment') }}</th>
                                    </thead>
                                    @foreach ($route['Parameters'] as $parameter)
                                        <tr>
                                            <td>{{ $parameter['name'] }}</td>
                                            <td>{{ $parameter['rules'] }}</td>
                                            <td>{{ $parameter['comment'] }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            <h6>{{ __('Returns') }}</h6>
                            <pre>{{ $route['Returns'] ?? __('NULL') }}</pre>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-dynamic-component>
