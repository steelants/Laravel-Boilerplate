<div>
    <h5 class="page-title mt-3">{{ __('Limitace') }}</h5>

    <x-form::form wire:submit.prevent="store">
        <x-form::select group-class="mb-3" :options="$this->limits" wire:model="limitation" id="limitation" label="{{ __('Itemů na stránku') }}"/>
        <x-form::button class="mb-3 btn-primary" type="submit">{{ __('Uložit') }}</x-form::button>
    </x-form::form>
</div>
