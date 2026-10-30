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
        <div class="mb-3">
            <label class="form-label" for="code">{{ __('Authentication code') }}:</label>
            <div @class(['otp', 'is-invalid' => $errors->has('code')]) data-bs-otp>
                <input type="text" class="otp-input" name="code" id="code" maxlength="6" autocomplete="one-time-code" aria-label="{{ __('Authentication code') }}">
            </div>
            @error('code')
                <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex">
            <x-form::button class="btn-solid theme-primary" type="submit">{{ __('boilerplate::auth.login') }}</x-form::button>
        </div>
    </form>
</div>
</x-layout-auth>
