<x-dynamic-component :component="$layout">
	<div class="container-xl">
		<div class="page-header">
			<h1>{{ __("Jobs") }}</h1>
			<button class="btn btn-danger" onclick="confirm('{{ __('Do you really want to clear all jobs?') }}') ? window.location.href = '{{ route('system.jobs.clear') }}' : false">
				<i class="me-2 fas fa-trash"></i>
				<span>{{ __('Clear jobs') }}</span>
			</button>
		</div>

		<h5>{{ __('Start job') }}</h5>
		<div class="">
			@foreach ($jobs_classes as $job)
				<x-form::button class="btn-primary" group-class="mb-3"
					onclick="Livewire.dispatch('openModal', {livewireComponents: 'job.form', title:  '{{ $job }}', parameters: {job: '{{ $job }}'}})">{{ $job }}</x-form::button>
			@endforeach
		</div>

		<div class="d-flex align-items-center justify-content-between mt-4">
			<h5 class="mb-0">{{ __("Waiting") }} <span class="badge text-bg-secondary">{{ $waiting_count }}</span></h5>
			<button class="btn btn-warning btn-sm" onclick="confirm('{{ __('Do you really want to stop all waiting jobs?') }}') ? window.location.href = '{{ route('system.jobs.stop') }}' : false">
				<i class="me-2 fas fa-stop"></i>
				<span>{{ __('Stop jobs') }}</span>
			</button>
		</div>
        @livewire('job.data-table', [], key('data-table'))

		<div class="d-flex align-items-center justify-content-between mt-4">
			<h5 class="mb-0">{{ __("Failed") }} <span class="badge text-bg-secondary">{{ $failed_count }}</span></h5>
			<button class="btn btn-success btn-sm" onclick="confirm('{{ __('Do you really want to rerun all failed jobs?') }}') ? window.location.href = '{{ route('system.jobs.rerun') }}' : false">
				<i class="me-2 fas fa-redo"></i>
				<span>{{ __('Rerun jobs') }}</span>
			</button>
		</div>
        @livewire('job.data-table', ['failed' => true], key('data-table'))
	</div>
</x-dynamic-component>
