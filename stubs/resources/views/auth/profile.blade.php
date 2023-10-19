@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('user.ShowUser') }}</h1>
            <a class="btn btn-primary"
                href="{{ url()->previous() }}">{{ __('boilerplate::ui.back') }}</a>
        </div>

        <form action="{{ route('profile.update', ['user' => $user]) }}" method="POST">
            @csrf
            @method('put')

            <div>
                <h4>{{ __('user.UserInfo') }}</h4>
            </div>

            <div class="form-group">
                <label class="form-label"><b>{{ __('boilerplate::ui.name') }}:</b></label>
                <p>{{ $user->name ?? '' }}</p>
            </div>

            <div class="form-group">
                <label class="form-label"><b>{{ __('boilerplate::ui.email') }}:</b></label>
                <p>{{ $user->email ?? '' }}</p>
            </div>

            <div>
                <h4>{{ __('user.PasswordChange') }}</h4>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">{{ __('boilerplate::ui.old.password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="newPassword">{{ __('boilerplate::ui.new.password') }}</label>
                <input id="newPassword" type="password" class="form-control @error('newPassword') is-invalid @enderror"
                    name="newPassword">

                @error('newPassword')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="newPassword-confirm">{{ __('boilerplate::ui.confirm.password') }}</label>
                <input id="newPassword-confirm" type="password" class="form-control" name="newPassword_confirmation"
                    >
            </div>

            <button type="submit" class="btn btn-primary">{{ __('boilerplate::ui.update') }}</button>
        </form>
    @endsection