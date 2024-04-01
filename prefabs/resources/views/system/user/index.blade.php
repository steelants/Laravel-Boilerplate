<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.users') }}</h1>
            <button class="btn btn-primary" onclick="Livewire.dispatch('openModal', {livewireComponents: 'user.form', title:  'Create user'})">
                <i class="me-2 fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
            </button>
        </div>
        @livewire('user.data-table', [], key('data-table'))
    </div>
</x-layout-app>
