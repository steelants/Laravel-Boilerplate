<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('Logs')}}</h1>
            <x-boilerplate::form-button :action="route('system.log.clear')" :confirm="__('Do you really want to clear all logs?')" :label="__('Clear logs')" />
        </div>

        <div class="row g-3 mb-4">
            @foreach ($todayStats as $stat => $value)
                <div class="md:col-3">
                    <div class="card">
                        <div class="card-body ">
                            <div class="h5 fw-medium">{{ $stat }}</div>
                            <div class="h1 mb-0 {{$stat == "ERROR" ? "fg-danger" : "" }}{{$stat == "WARNING" ? "fg-warning" : "" }}">{{ $value ?? '??' }}</div>
                            <small class="fg-4">{{ __('today') }}</small>
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
                                <a href='{{ route('system.log.download', ['file' => $item['fileName']]) }}' class="btn btn-solid theme-primary">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <x-boilerplate::form-button :action="route('system.log.delete', ['file' => $item['fileName']])" :confirm="__('Are you sure?')" :title="__('Remove')" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-dynamic-component>
