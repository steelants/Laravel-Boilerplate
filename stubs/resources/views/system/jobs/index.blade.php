@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="layout-header">
            <h3 class="page-title">{{ __('boilerplate::ui.jobs')}}</h3>
        </div>

        <div class="table-responsive">
            <table class="table mt-2">
                <thead>
                    <tr>
                        <th scope="col">uuid</th>
                        <th scope="col">queue</th>
                        <th scope="col">payload</th>
                        <th scope="col">exception</th>
                        <th scope="col">failed_at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <th scope="row">
                                {{ $item->uuid }}
                            </th>
                            <td>
                                {{ $item->queue }}
                            </td>
                            <td>
                                <div class="text-break">{{ Str::substr($item->payload, 0, 150)}}</div>
                            </td>
                            <td>
                                <div class="text-break">{{ Str::substr($item->exception, 0, 300)}}</div>
                            </td>
                            <td>
                                {{ $item->failed_at }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
