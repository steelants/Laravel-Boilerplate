@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="page-header">
            <h1>{{ __('user.ShowUser') }}</h1>
            <a class="btn btn-primary"
                href="{{ url()->previous() }}">{{ __('web.Back') }}</a>
        </div>

        <form action="{{ route('profile.update', ['user' => $user]) }}" method="POST">
            @csrf
            @method('put')

            <div>
                <h4>{{ __('user.UserInfo') }}</h4>
            </div>

            <div class="form-group">
                <label class="form-label"><b>{{ __('auth.Name') }}:</b></label>
                <p>{{ $user->name ?? '' }}</p>
            </div>

            <div class="form-group">
                <label class="form-label"><b>{{ __('auth.EmailAddress') }}:</b></label>
                <p>{{ $user->email ?? '' }}</p>
            </div>

            <div>
                <h4>{{ __('user.PasswordChange') }}</h4>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">{{ __('auth.OldPassword') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="newPassword">{{ __('auth.NewPassword') }}</label>
                <input id="newPassword" type="password" class="form-control @error('newPassword') is-invalid @enderror"
                    name="newPassword">

                @error('newPassword')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="newPassword-confirm">{{ __('auth.ConfirmPassword') }}</label>
                <input id="newPassword-confirm" type="password" class="form-control" name="newPassword_confirmation"
                    >
            </div>

            <button type="submit" class="btn btn-primary">{{ __('user.Update') }}</button>
        </form>
    @endsection