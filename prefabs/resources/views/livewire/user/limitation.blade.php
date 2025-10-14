<div>
    <h5 class="page-title mt-3">{{ __('Limitation') }}</h5>

    <x-form::form wire:submit.prevent="store">
        <x-form::select group-class="mb-3" :options="$this->limits" wire:model="limitation" id="limitation" label="{{ __('Items per page') }}"/>
        <x-form::button class="mb-3 btn-primary" type="submit">{{ __('Save') }}</x-form::button>
    </x-form::form>
</div>
