<div>
    <p class="text-muted">{{ __('Select components to restore. This operation will overwrite current data.') }}</p>
    <x-form::form wire:submit.prevent="store">
        @foreach ($this->fields as $field)
            <x-form-components::field
                :field="'properties.'.$field"
                :label="$this->labels[$field] ?? $field"
                :help="$this->helps[$field] ?? null"
                :type="$this->types[$field] ?? null"
            />
        @endforeach
        <x-form::button class="btn-warning" type="submit">
            <i class="fa fa-undo me-1"></i>{{ __('Restore') }}
        </x-form::button>
    </x-form::form>
</div>
