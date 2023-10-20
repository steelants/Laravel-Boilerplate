@extends('layouts.auth')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-xl-6">
        <h2 class="mb-3">{{ __('boilerplate::ui.reset_password') }}</h2>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
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

            <button type="submit" class="btn btn-primary w-100">
                {{ __('boilerplate::ui.reset_password') }}
            </button>
        </form>

        @if (Route::has('login'))
            <div class="text-center mt-4">
                {{ __('boilerplate::ui.back_to') }}:
                <a href="{{ route('login') }}">{{ __('boilerplate::ui.login') }}</a>
            </div>
        @endif
    </div>
</div>
@endsection
