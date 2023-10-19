@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-xl-6">
        <h2 class="mb-3">{{ __('auth.ResetPassword') }}</h2>
        
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('auth.EmailAddress') }}</label>

                <div class="">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('auth.Password') }}</label>

                <div class="">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label">{{ __('auth.ConfirmPassword') }}</label>

                <div class="">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                {{ __('auth.ResetPassword') }}
            </button>
        </form>

        @if (Route::has('login'))
            <div class="text-center mt-4">
                {{ __('auth.BackTo')}}
                <a href="{{ route('login') }}">{{ __('auth.title.Login') }}</a>
            </div>
        @endif
        
    </div>
</div>
@endsection
