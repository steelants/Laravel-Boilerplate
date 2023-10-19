@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-xl-6">
        <h2 class="mb-3">{{ __('auth.ConfirmPassword') }}</h2>

        {{ __('auth.confirmMessage') }}

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('auth.Password') }}</label>

                <div class="">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                {{ __('auth.ConfirmPassword') }}
            </button>

            @if (Route::has('password.request'))
                <a class="btn btn-link w-100" href="{{ route('password.request') }}">
                    {{ __('auth.Forgot') }}
                </a>
            @endif
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
