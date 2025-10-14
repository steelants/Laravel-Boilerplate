<x-layout-auth>
    <x-form::form method="POST" action="{{ route('login.submit') }}">
        <x-form::input class="mb-3" type="email" name="email" id="email" label="{{ __('Email') }}:" />
        <x-form::input class="mb-3" type="password" name="password" id="password" label="{{ __('Password') }}:" />
        <x-form::checkbox name="remember" id="remember" label="{{ __('Remember me') }}:" />

        <div class="d-flex">
            <x-form::button class="btn-primary" type="submit">{{ __('Login') }}</x-form::button>
            @if (Route::has('register'))
                <a class="ms-2 btn btn-primary" href="{{ route('register') }}">
                    {{ __('Register') }}
                </a>
            @endif
            @if (Route::has('password'))
                <a class="ms-auto text-nowrap" href="{{ route('password') }}">
                    {{ __('Password Reset') }}
                </a>
            @endif
        </div>
    </x-form::form>
</x-layout-auth>
