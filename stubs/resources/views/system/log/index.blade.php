@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.log')}}</h1>
        </div>

        <div class="row mb-4">
            @foreach ($todayStats as $stat => $value)
                <div class="col-4">
                    <div class="card {{$stat == "ERROR" ? "bg-danger" : "" }}{{$stat == "WARNING" ? "bg-warning" : "" }}">
                        <div class="card-body ">
                            <div class="h5 fw-medium">{{ $stat }}</div>
                            <div class="h1 mb-0">{{ $value }}</div>
                            <small class="text-body-tertiary">today</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table mt-2">
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
@endsection