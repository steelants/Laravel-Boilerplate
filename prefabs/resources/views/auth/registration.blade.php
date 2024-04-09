<x-layout-auth>
    <x-form::form method="POST" action="{{ route('register.submit') }}">
        <x-form::input class="mb-3" type="text" name="name" id="name" label="{{ __('boilerplate::auth.name') }}:" placeholder="JohnDoe" />
        <x-form::input class="mb-3" type="email" name="email" id="email" label="{{ __('boilerplate::auth.email') }}:" placeholder="email@post.xx" />
        <x-form::input class="mb-3" type="password" name="password" id="password" label="{{ __('boilerplate::auth.password') }}:" />
        <x-form::input class="mb-3" type="password" name="password_confirmation" id="password_confirmation" label="{{ __('boilerplate::auth.password_confirm') }}:" />

        <div class="d-flex">
            <x-form::button class="p-2 btn-primary" type="submit">{{ __('boilerplate::auth.register') }}</x-form::button>
            @if (Route::has('login'))
                <a class="ms-auto text-nowrap" href="{{ route('login') }}">
                    {{ __('boilerplate::auth.login') }}
                </a>
            @endif
        </div>
    </x-form::form>
</x-layout-auth>
