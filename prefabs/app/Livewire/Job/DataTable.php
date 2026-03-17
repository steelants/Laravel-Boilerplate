<?php

namespace App\Livewire\Job;

use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use SteelAnts\DataTable\Traits\UseDatabase;
use SteelAnts\LaravelBoilerplate\Models\FailedJob;
use SteelAnts\LaravelBoilerplate\Models\Job;

class DataTable extends DataTableComponent
{
    use UseDatabase;

    public bool $failed = false;
	public $listeners = [
		'jobAdded'   => '$refresh',
		'closeModal' => '$refresh',
	];

    public function query(): Builder
    {
        if ($this->failed) {
            return FailedJob::query();
        }
        return Job::query();
    }

    public function headers(): array
    {
        if ($this->failed) {
            return [
                'uuid'      => __("UUID"),
                'queue'     => __("Queue"),
                'name'      => __("Name"),
                'failed_at' => __("Failed At"),
            ];
        }

        return [
            'uuid'         => __("UUID"),
            'queue'        => __("Queue"),
            'name'         => __("Name"),
            'available_at' => __("Available At"),
        ];
    }

    public function row(Job|FailedJob $row): array
    {
        if ($this->failed) {
            return [
                'id'        => $row->id,
                'uuid'      => $row->payload['uuid'],
                'name'      => $row->payload['displayName'],
                'failed_at' => $row->failed_at,
                'queue'     => '['. $row->connection.'] ' . $row->queue,
            ];
        }

        return [
            'id'           => $row->id,
            'uuid'         => $row->payload['uuid'],
            'name'        => $row->payload['displayName'],
            'available_at' => $row->available_at,
            'queue'        =>  '['. $row->connection.'] ' . $row->queue,
        ];
    }

    public function actions($item)
    {
        if ($this->failed) {
            return [
				[
                    'type'        => "livewire",
                    'action'      => "trace",
                    'parameters'  => $item['uuid'],
                    'text'        => __("Trace"),
                    'actionClass' => '',
                    'iconClass'   => 'fas fa-bug',
                ],
                [
                    'type'        => "livewire",
                    'action'      => "retry",
                    'parameters'  => $item['uuid'],
                    'text'        => __("Retry"),
                    'actionClass' => '',
                    'iconClass'   => 'fas fa-sync',
                ],
            ];
        }

        return [
            [
                'type'        => "livewire",
                'action'      => "stop",
                'parameters'  => $item['id'],
                'text'        => __("Stop"),
                'actionClass' => '',
                'iconClass'   => 'fas fa-stop',
            ],
        ];
    }

    public function stop($job_id)
    {
		#Gate::authorize('is-admin');
        Job::find($job_id)->delete();
    }

    public function trace($job_uuid)
    {
		#Gate::authorize('is-admin');
		$this->dispatch('openModal', 'job.trace', __('Trace'), ['job_uuid' => $job_uuid]);
    }

	public function retry($job_uuid)
    {
		#Gate::authorize('is-admin');
        Artisan::call('queue:retry', ['id' => [$job_uuid]]);
    }
}
