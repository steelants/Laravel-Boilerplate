<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.audit') }}</h1>
        </div>
        @livewire('audit.data-table', [], key('data-table'))
    </div>
</x-layout-app>
