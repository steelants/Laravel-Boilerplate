<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::setting.title') }}</h1>
        </div>

        @livewire('boilerplate.settings.form', ['key' => "main.general.default"])
    </div>
</x-dynamic-component>
