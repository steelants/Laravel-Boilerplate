<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('File') }}</h1>
        </div>
        @livewire('boilerplate.file.gallery', [], key('data-table'))
    </div>
</x-dynamic-component>
