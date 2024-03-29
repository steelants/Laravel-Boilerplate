<div>
    <x-form::form wire:submit.prevent="{{ $action }}">
        <x-form::select wire:model="tier" group-class="mb-3" label="{{ __('boilerplate::subscriptions.tier') }}" :options="$tiers"/>

        <x-form::input group-class="mb-3" type="date" wire:model="valid_to" id="valid_to"
            label="{{ __('boilerplate::subscriptions.valid_to') }}" />

        @if ($action == 'update')
            <x-form::button class="btn-primary" type="submit">{{ __('boilerplate::ui.add') }}</x-form::button>
        @else
            <x-form::button class="btn-primary" type="submit">{{ __('boilerplate::ui.add') }}</x-form::button>
        @endif
    </x-form::form>
</div>
