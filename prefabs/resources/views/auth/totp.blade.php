<x-layout-auth>
<div>
    <h1>{{ __('Two-factor authentication') }}</h1>
    @if(!$hasTotp)
        <img src="{{ $qrDataUri }}" alt="TOTP QR">
        <div>
            <p>{{ __('Secret') }}</p>
            <strong>{{ $secret }}</strong>
        </div>
    @else
        <p>{{ __('Enter your current TOTP code to continue.') }}</p>
    @endif

    <form method="POST" action="{{ route('totp.verify') }}">
        @csrf
        <x-form::input class="mb-3" type="text" name="code" id="code" label="{{ __('Authentication code') }}:" />
        <div class="d-flex">
            <x-form::button class="btn-primary" type="submit">{{ __('boilerplate::auth.login') }}</x-form::button>
        </div>
    </form>
</div>
</x-layout-auth>
