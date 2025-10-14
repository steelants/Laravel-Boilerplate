<x-dynamic-component :component="$layout">
	<div class="container-xl">
		<div class="page-header">
			<h1>{{ __("Jobs") }}</h1>
			<button class="btn btn-danger" onclick="confirm('{{ __('Are you shure?') }}') ? window.location.href = '{{ route('system.jobs.clear') }}' : false">{{ __("Clear jobs") }}</button>
		</div>

		<h5>{{ __('Start job') }}</h5>
		<div class="">
			@foreach ($jobs_classes as $job)
				<x-form::button class="btn-primary" group-class="mb-3"
					onclick="Livewire.dispatch('openModal', {livewireComponents: 'job.form', title:  '{{ $job }}', parameters: {job: '{{ $job }}'}})">{{ $job }}</x-form::button>
			@endforeach
		</div>

		<h5 class="mt-4">{{ __("Waiting") }} <span class="badge text-bg-secondary">{{ $waiting_count }}</span></h5>
        @livewire('job.data-table', [], key('data-table'))

		<h5 class="mt-4">{{ __("Failed") }} <span class="badge text-bg-secondary">{{ $failed_count }}</span></h5>
        @livewire('job.data-table', ['failed' => true], key('data-table'))
	</div>
</x-dynamic-component>
