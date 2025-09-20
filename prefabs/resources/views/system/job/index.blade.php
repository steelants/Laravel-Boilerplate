<x-dynamic-component :component="$layout">
	<div class="container-xl">
		<div class="page-header">
			<h1>{{ __("boilerplate::ui.jobs") }}</h1>
			<button class="btn btn-danger" onclick="confirm('{{ __("boilerplate::ui.jobs-clear-confirm") }}') ? window.location.href = '{{ route("system.jobs.clear") }}' : false">{{ __("boilerplate::ui.jobs-clear") }}</button>
		</div>

		<h5>{{ __("boilerplate::ui.jobs-waiting") }}</h5>
		<div class="table-responsive mb-4">
			<table class="table">
				<thead>
					<tr>
						<th scope="col">id</th>
						<th scope="col">queue</th>
						<th scope="col">payload</th>
						<th scope="col">available_at</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($jobs as $item)
						<tr>
							<th scope="row">
								{{ $item->id }}
							</th>
							<td>
								{{ $item->queue }}
							</td>
							<td>
								<div class="text-break">{{ Str::substr($item->payload, 0, 150) }}</div>
							</td>
							<td>
								{{ date("Y-m-d H:i:s", $item->available_at) }}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<h5>{{ __("boilerplate::ui.jobs-failed") }}</h5>
		<div class="table-responsive mb-4">
			<table class="table">
				<thead>
					<tr>
						<th scope="col">uuid</th>
						<th scope="col">queue</th>
						<th scope="col">payload</th>
						<th scope="col">exception</th>
						<th scope="col">failed_at</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($failed_jobs as $item)
						<tr>
							<th scope="row">
								{{ $item->uuid }}
							</th>
							<td>
								{{ $item->queue }}
							</td>
							<td>
								<div class="text-break">{{ Str::substr($item->payload, 0, 150) }}</div>
							</td>
							<td>
								<div class="text-break">{{ Str::substr($item->exception, 0, 300) }}</div>
							</td>
							<td>
								{{ $item->failed_at }}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<h5>{{ __('boilerplate::ui.jobs-start') }}</h5>
		<div class="">
			@foreach ($jobs_classes as $job)
				<x-form::button class="btn-primary" group-class="mb-3"
					onclick="Livewire.dispatch('openModal', {livewireComponents: 'job.form', title:  '{{ $job }}', parameters: {job: '{{ $job }}'}})">{{ $job }}</x-form::button>
			@endforeach
		</div>
	</div>
</x-dynamic-component>
