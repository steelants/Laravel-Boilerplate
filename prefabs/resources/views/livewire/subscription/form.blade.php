<div>
    <x-form::form wire:submit.prevent="{{ $action }}">
        <x-form::select wire:model="tier" group-class="mb-3" label="{{ __('Tier') }}" :options="$tiers"/>

        <x-form::input group-class="mb-3" type="date" wire:model="valid_to" id="valid_to"
            label="{{ __('Valid to') }}" />

        @if ($action == 'update')
            <x-form::button class="btn-primary" type="submit">{{ __('Add') }}</x-form::button>
        @else
            <x-form::button class="btn-primary" type="submit">{{ __('Add') }}</x-form::button>
        @endif
    </x-form::form>
</div>
