<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __($title) }}</h1>
            @if(isset($modal_component))
                <button class="btn btn-primary" onclick="Livewire.dispatch('openModal', {livewireComponents: '{{$modal_component}}', title: '{{ __('boilerplate::model.create') }}'})">
                    <i class="me-2 fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
                </button>
            @endif
        </div>

        @livewire($page_component, [], key('data-table'))
    </div>
</x-layout-app>
