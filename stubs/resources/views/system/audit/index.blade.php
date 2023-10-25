@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.audit') }}</h1>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">{{ __('boilerplate::ui.created') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.ip_address') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.note') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.author') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.model') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($activities) && $activities->count() > 0)
                        @foreach ($activities as $activity)
                            <tr>
                                <th scope="row">{{ $activity->created_at }}</th>
                                <td>{{ $activity->ip }}</td>
                                <td>{{ $activity->lang_text }}</td>
                                <td>{{ !empty($activity->user) ? $activity->user->name : (isset($activity->user_id) && $activity->user_id === 0 ? __('user.Console') : __('user.UserDeleted')) }}</td>
                                <td>
                                     @if (isset($activity->affected))
                                        {{ json_encode([
                                            'id'=>$activity->affected->id,
                                            'name'=>$activity->affected->name,
                                        ], JSON_UNESCAPED_UNICODE) }}
                                    @else
                                        @if (isset($urls[$activity->id]))
                                            @if ($urls[$activity->id] != '')
                                                <a href="{{ $urls[$activity->id] }}">{{ __('boilerplate::ui.show') }}</a>
                                            @else
                                                {{ __('boilerplate::ui.deleted') }}
                                            @endif
                                        @else
                                            {{ __('boilerplate::ui.deleted') }}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="5">{{ __('boilerplate::ui.not_found') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
