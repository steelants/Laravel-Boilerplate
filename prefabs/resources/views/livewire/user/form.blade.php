<div>
    <x-form::form wire:submit.prevent="store">
        <x-form::input group-class="mb-3" type="text" wire:model="name" id="name" label="{{ __('boilerplate::ui.name') }}"/>
        <x-form::input group-class="mb-3" type="email" wire:model="email" id="email" label="{{ __('boilerplate::ui.email') }}"/>
        <x-form::input group-class="mb-3" type="password" wire:model="password" id="password" label="{{ __('boilerplate::ui.password') }}"/>
        <x-form::input group-class="mb-3" type="password" wire:model="password_confirmation" id="password_confirmation" label="{{ __('boilerplate::ui.confirm.password') }}"/>
        <x-form::button class="btn-primary" type="submit">{{ __('boilerplate::ui.create') }}</x-form::button>
    </x-form::form>
</div>