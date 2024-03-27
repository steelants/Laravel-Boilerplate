<x-layout-auth>
    <x-form::form method="POST" action="{{ route('register.submit') }}">
        <x-form::input group-class="mb-3" type="text" name="name" id="name" label="{{ __('Name') }}:" placeholder="JohnDoe" />
        <x-form::input group-class="mb-3" type="email" name="email" id="email" label="{{ __('Email') }}:" placeholder="email@post.xx" />
        <x-form::input group-class="mb-3" type="password" name="password" id="password" label="{{ __('Password') }}:" />
        <x-form::input group-class="mb-3" type="password" name="password_confirmation" id="password_confirmation" label="{{ __('Confirm Password') }}:" />
        <x-form::button class="btn-primary" type="submit">{{ __('Register') }}</x-form::button>
    </x-form::form>
</x-layout-auth>


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
        
        @if (Route::has('register'))
            <a href="{{ route('register') }}">
                {{ __('Registration') }} ?
            </a>
        @endif
    </x-form::form>
</x-layout-auth>
