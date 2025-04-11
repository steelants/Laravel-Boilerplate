<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.api') }}</h1>
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
                            title="{{ __('boilerplate:ui.copy_to_clipboard') }}"
                        >
                            <span>{{ $route['Uri'] }}</span>
                            <i class="fa fa-clipboard"></i>
                        </b>
                        <a class="stretched-link" data-bs-target="#collapse{{ $id }}" data-bs-toggle="collapse"></a>
                    </div>
                    <div class="collapse" id="collapse{{ $id }}">
                        <div class="card card-body mt-2">
                            <h6>Description</h6>
                            <pre>{{ $route['Description'] }}</pre>

                            <h6>Parameters</h6>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Comment</th>
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

                            <h6>Returns</h6>
                            <pre>{{ $route['Returns'] ?? 'NULL' }}</pre>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layout-app>
