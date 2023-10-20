@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-xl-6">
        <h2 class="mb-3">{{ __('boilerplate::ui.login') }}</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('boilerplate::ui.email') }}</label>

                <div class="">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('boilerplate::ui.password') }}</label>

                <div class="">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <div class="row">
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('boilerplate::ui.remember_me') }}
                            </label>
                        </div>
                    </div>

                    <div class="col-6 text-end">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                {{ __('boilerplate::ui.forgot.password') }} ?
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                {{ __('boilerplate::ui.login') }}
            </button>
        </form>

        @if (Route::has('register'))
            <div class="text-center mt-4">
                {{ __('boilerplate::ui.dont_have_account_yet')}} ?
                <a href="{{ route('register') }}">{{ __('boilerplate::ui.register') }}</a>
            </div>
        @endif
    </div>
</div>
@endsection
