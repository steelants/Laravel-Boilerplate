<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __($title) }}</h1>
			@if(!empty($model_back))
				<a class="btn btn-secondary" href="{{ $model_back }}">
                    <i class="me-2 fas fa-arrow-left"></i><span>{{ __('boilerplate::ui.back') }}</span>
                </a>
            @endif
            @if(isset($modal_component))
                <button class="btn btn-primary" onclick="Livewire.dispatch('openModal', {livewireComponents: '{{$modal_component}}', title: '{{ __('boilerplate::model.create') }}'})">
                    <i class="me-2 fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
                </button>
			@elseif(isset($full_page_component))
				<a class="btn btn-primary" href="{{ route($full_page_component) }}">
                    <i class="me-2 fas fa-plus"></i><span>{{ __('boilerplate::ui.add') }}</span>
                </a>
            @endif
        </div>

        @livewire($page_component, (isset($data) ? $data : []), key($page_component))
    </div>
</x-layout-app>
