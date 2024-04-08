<x-layout-auth>
    <x-form::form method="POST" action="{{ isset($token) ? route('password.update') : route('password.email') }}">
        @if (isset($token))
            <x-form::input type="hidden" id="token" name="token" value="{{ $token }}" />
            <x-form::input class="mb-3" type="email" id="email" name="email" label="{{ __('boilerplate::auth.email') }}" value="{{ $email }}" />
            <x-form::input class="mb-3" type="password" id="password" name="password" label="{{ __('Password') }}" />
            <x-form::input class="mb-3" type="password" id="password_confirmation" name="password_confirmation" label="{{ __('boilerplate::auth.password_confirm') }}" />
        @else
            <x-form::input class="mb-3" type="email" id="email" name="email" label="{{ __('boilerplate::auth.email') }}" placeholder="email@post.xx" required/>
        @endif

        <div class="d-flex">
            <x-form::button class="btn-primary" type="submit">{{ __('boilerplate::auth.send_password_reset') }}</x-form::button>
            @if (Route::has('login'))
                <a class="ms-auto text-nowrap" href="{{ route('login') }}">
                    {{ __('boilerplate::auth.login') }} ?
                </a>
            @endif
        </div>
    </x-form::form>
</x-layout-auth>
