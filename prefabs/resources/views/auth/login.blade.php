<x-layout-auth>
    <x-form::form method="POST" action="{{ route('login.submit') }}">
        <x-form::input class="mb-3" type="email" name="email" id="email" label="{{ __('boilerplate::auth.email') }}:" />
        <x-form::input class="mb-3" type="password" name="password" id="password" label="{{ __('boilerplate::auth.password') }}:" />
        <x-form::input class="mb-3" type="checkbox" name="remmeber" id="remmeber" label="{{ __('boilerplate::auth.remember') }}:" />

        <div class="d-flex">
            <x-form::button class="btn-primary" type="submit">{{ __('boilerplate::auth.login') }}</x-form::button>
            @if (Route::has('register'))
                <a class="ms-2 btn btn-primary" href="{{ route('register') }}">
                    {{ __('boilerplate::auth.register') }}
                </a>
            @endif
            @if (Route::has('password'))
                <a class="ms-auto text-nowrap" href="{{ route('password') }}">
                    {{ __('boilerplate::auth.password_reset') }}
                </a>
            @endif
        </div>
    </x-form::form>
</x-layout-auth>
