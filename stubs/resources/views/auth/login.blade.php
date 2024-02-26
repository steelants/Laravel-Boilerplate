<x-layout-auth>
    <x-form::form method="POST" action="{{ route('login.submit') }}">
        <x-form::input group-class="mb-3" type="email" name="email" id="email" label="{{ __('Email') }}:" />
        <x-form::input group-class="mb-3" type="password" name="password" id="password" label="{{ __('Password') }}:" />
        @if (Route::has('password'))
            <a href="{{ route('password') }}">
                {{ __('Password Reset') }} ?
            </a><br>
        @endif

        <x-form::button class="btn-primary" type="submit">{{ __('Login') }}</x-form::button>
        
        @if (Route::has('registration'))
            <a href="{{ route('registration') }}">
                {{ __('Password Registration') }} ?
            </a>
        @endif
    </x-form::form>
</x-layout-auth>
