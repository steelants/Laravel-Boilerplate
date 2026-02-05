<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('Api') }}</h1>
        </div>
        <div>
            @foreach ($routes as $id => $route)
                @php
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
                    <div class="btn-toggle btn py-3 bg-{{ $color }} w-100 text-start position-relative" style="--bs-bg-opacity: .2;">
                        <b class="badge text-bg-{{ $color }} me-2" style="--bs-bg-opacity: 1;">{{ $route['Method'] }}</b>
                        <b class="position-relative z-2"
                            onclick="copyToClipboard('{{ config('app.url') . $route['Uri'] }}');"
                            title="{{ __('Copy to clipboard') }}"
                        >
                            <span>{{ $route['Uri'] }}</span>
                            <i class="fa fa-clipboard"></i>
                        </b>
                        <a class="stretched-link" data-bs-target="#collapse{{ $id }}" data-bs-toggle="collapse"></a>
                    </div>
                    <div class="collapse" id="collapse{{ $id }}">
                        <div class="card card-body mt-2">
                            <h6>{{ __('Description') }}</h6>
                            <pre>{{ $route['Description'] }}</pre>

                            <h6>{{ __('Parameters') }}</h6>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Comment') }}</th>
                                </thead>
                                @foreach ($route['Parameters'] as $parameter)
                                    <tr>
                                        <td>
                                            {{ $parameter['name'] }}
                                        </td>
                                        <td>
                                            {{ $parameter['type'] }}
                                        </td>
                                        <td>
                                            {{ $parameter['comment'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <h6>{{ __('Returns') }}</h6>
                            <pre>{{ $route['Returns'] ?? __('NULL') }}</pre>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-dynamic-component>
