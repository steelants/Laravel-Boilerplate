<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.file') }}</h1>
        </div>
        @livewire('boilerplate.file.gallery', [], key('data-table'))
    </div>
</x-layout-app>
