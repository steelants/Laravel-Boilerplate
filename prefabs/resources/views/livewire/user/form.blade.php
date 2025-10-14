<div>
    <x-form::form wire:submit.prevent="store">
        <x-form::input group-class="mb-3" type="text" wire:model="name" id="name" label="{{ __('Name') }}"/>
        <x-form::input group-class="mb-3" type="email" wire:model="email" id="email" label="{{ __('Email') }}"/>
        <x-form::input group-class="mb-3" type="password" wire:model="password" id="password" label="{{ __('Password') }}"/>
        <x-form::input group-class="mb-3" type="password" wire:model="password_confirmation" id="password_confirmation" label="{{ __('Confirm password') }}"/>
        <x-form::button class="btn-primary" type="submit">{{ __('Create') }}</x-form::button>
    </x-form::form>
</div>