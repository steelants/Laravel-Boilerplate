<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::{{$slug}}.title') }}</h1>

            <button class="btn btn-primary" onclick="Livewire.dispatch('openModal', {livewireComponents: '{{$slug}}.form', title: '{{ __('boilerplate::model.create') }}'})">
                <i class="me-2 fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
            </button>
        </div>

        @livewire('{{$slug}}.data-table', [], key('data-table'))
    </div>
</x-layout-app>
