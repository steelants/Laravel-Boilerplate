<div>
    patrik je borec na 2hou
    {{ $modelId }}
    <x-form::formlivewireAction="save">
        <x-form::input id="label" name="test" required=true livewireModel="test" />
        <x-form::input id="label2" name="test2" />
        <x-form::input id="label3" name="test3" />
        <x-form::input id="label4" name="test4" />
        <x-form::button text="label5" />
    </x-form>
</div>


@push('modal-header')
    FUCK OYU
@endpush

