<x-layout-auth>
    <x-form::form method="POST" action="{{ isset($token) ? route('password.update') : route('password.email') }}">
        @if (isset($token))
            <x-form::input type="hidden" id="token" name="token" value="{{ $token }}" />
            <x-form::input class="mb-3" type="email" id="email" name="email" label="{{ __('Email') }}" value="{{ $email }}" />
            <x-form::input class="mb-3" type="password" id="password" name="password" label="{{ __('Password') }}" />
            <x-form::input class="mb-3" type="password" id="password_confirmation" name="password_confirmation" label="{{ __('Configm Password') }}" />
        @else
            <x-form::input class="mb-3" type="email" id="email" name="email" label="{{ __('Email') }}" placeholder="email@post.xx" required/>
        @endif

        <div class="d-flex">
            <x-form::button class="btn-primary" type="submit">{{ __('Send password Reset') }}</x-form::button>
            @if (Route::has('login'))
                <a class="ms-auto text-nowrap" href="{{ route('login') }}">
                    {{ __('Login') }} ?
                </a>
            @endif
        </div>
    </x-form::form>
</x-layout-auth>
