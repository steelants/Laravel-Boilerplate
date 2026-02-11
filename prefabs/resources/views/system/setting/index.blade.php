<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('boilerplate::setting.title') }}</h1>
        </div>

        @livewire('boilerplate.setting.form', ['key' => "main.general"])
    </div>
</x-dynamic-component>
