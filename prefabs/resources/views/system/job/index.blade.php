<x-dynamic-component :component="$layout">
	<div class="container-xl">
		<div class="page-header">
			<h1>{{ __("Jobs") }}</h1>
			<x-boilerplate::form-button :action="route('system.jobs.clear')" :confirm="__('Do you really want to clear all jobs?')" :label="__('Clear jobs')" />
		</div>

		<div class="page-header mb-2">
			<h5>{{ __('Start job') }}</h5>
		</div>
		<div class="">
			@foreach ($jobs_classes as $job)
				<x-form::button class="btn-primary" group-class="mb-3"
					onclick="Livewire.dispatch('openModal', {livewireComponents: 'job.form', title:  '{{ $job }}', parameters: {job: '{{ $job }}'}})">{{ $job }}</x-form::button>
			@endforeach
		</div>

		<div class="page-header mb-2 mt-4">
			<h5>
				{{ __("Waiting") }} <span class="badge text-bg-secondary">{{ $waiting_count }}</span>
				<span class="badge text-bg-primary">{{ __('Processing') }} {{ $processing_count }}</span>
			</h5>
			<x-boilerplate::form-button :action="route('system.jobs.stop')" :confirm="__('Do you really want to stop all waiting jobs?')" :label="__('Stop jobs')" variant="warning" icon="fa-solid fa-stop" class="btn-sm" />
		</div>
        @livewire('job.data-table', [], key('waiting-jobs'))

		<div class="page-header mb-2 mt-4">
			<h5>{{ __("Failed") }} <span class="badge text-bg-secondary">{{ $failed_count }}</span></h5>
			<x-boilerplate::form-button :action="route('system.jobs.rerun')" :confirm="__('Do you really want to rerun all failed jobs?')" :label="__('Rerun jobs')" variant="success" icon="fa-solid fa-redo" class="btn-sm" />
		</div>
        {{-- Both tables are the same component and DataTableComponent::queryString() has no
             per-instance prefix, so the second one would pick up the first one's sortBy from
             the URL - e.g. "status", which failed_jobs has no column for. --}}
        @livewire('job.data-table', ['failed' => true, 'useUrl' => false], key('failed-jobs'))
	</div>
</x-dynamic-component>
