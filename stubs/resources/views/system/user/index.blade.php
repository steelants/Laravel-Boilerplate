@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.users') }}</h1>
            <button class="btn btn-primary" onclick="Livewire.emit('openModal', 'user.form', '{{ __('user.create') }}')">
                <i class="nav-ico fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
            </button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('boilerplate::ui.name') }}</th>
                        <th scope="col">{{ __('boilerplate::ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <th scope="row">{{ $user->name }}</th>
                            <th>
                                <div class="d-flex flex-row bd-highlight mb-3">
                                    <form action="{{ route('system.user.remove', ['user_id' => $user->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <x-form-submit class="btn-danger" text="{{ __('boilerplate::ui.remove') }}"
                                            confirmation="true" />
                                    </form>
                                    @if (Route::has('password.request'))
                                        <form action="{{ route('password.email') }}" method="post">
                                            @csrf
                                            <input class="form-control" id="email-{{ $user->id}}" name="email" type="hidden"
                                                value="{{ $user->email }}" autocomplete="email" autofocus required>
                                        </form>
                                    @endif
                                </div>
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
