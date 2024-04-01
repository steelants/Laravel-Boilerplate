<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.log')}}</h1>
             <button onclick="confirm('{{ __('boilerplate::ui.log-clear-confirm') }}') ? window.location.href = '{{ route('system.log.clear') }}' : false" class="btn btn-danger">{{ __('boilerplate::ui.logs-clear') }}</button>
        </div>

        <div class="row g-3 mb-4">
            @foreach ($todayStats as $stat => $value)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body ">
                            <div class="h5 fw-medium">{{ $stat }}</div>
                            <div class="h1 mb-0 {{$stat == "ERROR" ? "text-danger" : "" }}{{$stat == "WARNING" ? "text-warning" : "" }}">{{ $value }}</div>
                            <small class="text-body-tertiary">today</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('boilerplate::ui.name')}}</th>
                        <th scope="col">{{ __('boilerplate::ui.size')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <th scope="row">
                                <a href="{{ route('system.log.detail', ['file' => $item['fileName']]) }}#end">{{ $item['fileName'] }}</a>
                            </th>
                            <td>
                                {{ $item['humanReadableSize'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout-app>
