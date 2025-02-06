<div>
    <x-form::form wire:submit.prevent="run('{{ $job }}')">
        @if (!empty($note))
            <pre class="alert alert-info mb-2">{{ $note }}</pre>
        @endif

        @foreach ($job_parameters as $name => $parameter_property)
            <x-form::input
                groupClass="mb-3"
                wire:model="job_parameters_value.{{ $name }}"
                type="text"
                id="job_parameters_value.{{ $name }}"
                key="job_parameters_value.{{ $name }}"
                label="{{ $name }}"
            />
        @endforeach

        <x-form::button class="btn-primary" type="submit">Run</x-form::button>
    </x-form::form>
</div>
