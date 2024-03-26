<x-layout-auth>
    <x-form::form method="POST" action="{{ isset($token) ? route('password.update') : route('password.email') }}">
        @if (isset($token))
            <x-form::input type="hidden" id="token" name="token" value="{{ $token }}" />
            <x-form::input group-class="mb-3" type="email" id="email" name="email" label="{{ __('Email') }}" value="{{ $email }}" />
            <x-form::input group-class="mb-3" type="password" id="password" name="password" label="{{ __('Password') }}" />
            <x-form::input group-class="mb-3" type="password" id="password_confirmation" name="password_confirmation" label="{{ __('Confirm Password') }}" />
        @else
            <x-form::input group-class="mb-3" type="email" id="email" name="email" label="{{ __('Email') }}" placeholder="email@post.xx" />
        @endif
        <x-form::button class="btn-primary" type="submit">{{ __('Send Password Reset Link') }}</x-form::button>
        <a href="{{ route('login') }}">
            {{ __('Login') }} ?
        </a>
    </x-form::form>
</x-layout-auth>
