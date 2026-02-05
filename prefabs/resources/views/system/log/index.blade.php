<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('Logs')}}</h1>
            <button onclick="confirm('{{ __('Do you really want to clear all logs?') }}') ? window.location.href = '{{ route('system.log.clear') }}' : false" class="btn btn-danger">{{ __('Clear logs') }}</button>
        </div>

        <div class="row g-3 mb-4">
            @foreach ($todayStats as $stat => $value)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body ">
                            <div class="h5 fw-medium">{{ $stat }}</div>
                            <div class="h1 mb-0 {{$stat == "ERROR" ? "text-danger" : "" }}{{$stat == "WARNING" ? "text-warning" : "" }}">{{ $value }}</div>
                            <small class="text-body-tertiary">{{ __('today') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Name')}}</th>
                        <th scope="col">{{ __('Size')}}</th>
                        <th scope="col" class="text-end">{{ __('Actions') }}</th>
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
                            <td class="text-end">
                                <a href='{{ route('system.log.download', ['file' => $item['fileName']]) }}' class="btn btn-primary">
                                    <i class="fa fa-download"></i>
                                </a>
                                <a href='{{ route('system.log.delete', ['file' => $item['fileName']]) }}' onclick="return confirm('{{ __('Are you sure?') }}')" class="btn btn-danger">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-dynamic-component>
