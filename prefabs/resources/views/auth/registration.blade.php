<x-layout-auth>
    <x-form::form method="POST" action="{{ route('register.submit') }}">
        <x-form::input class="mb-3" type="text" name="name" id="name" label="{{ __('Name') }}:" placeholder="JohnDoe" />
        <x-form::input class="mb-3" type="email" name="email" id="email" label="{{ __('Email') }}:" placeholder="email@post.xx" />
        <x-form::input class="mb-3" type="password" name="password" id="password" label="{{ __('Password') }}:" />
        <x-form::input class="mb-3" type="password" name="password_confirmation" id="password_confirmation" label="{{ __('Confirm Password') }}:" />

        <div class="d-flex">
            <x-form::button class="p-2 btn-primary" type="submit">{{ __('Register') }}</x-form::button>
            @if (Route::has('login'))
                <a class="ms-auto text-nowrap" href="{{ route('login') }}">
                    {{ __('Login') }}
                </a>
            @endif
        </div>
    </x-form::form>
</x-layout-auth>
