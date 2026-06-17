<?php

namespace App\Livewire\Job;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use SteelAnts\DataTable\Traits\UseDatabase;
use SteelAnts\LaravelBoilerplate\Models\FailedJob;
use SteelAnts\LaravelBoilerplate\Models\Job;
use SteelAnts\LaravelBoilerplate\RenderCasts\FormatDateTime;

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

    public function row(Job|FailedJob $row): array
    {
        if ($this->failed) {
            return [
                'id'        => $row->id,
                'uuid'      => $row->payload['uuid'],
                'name'      => $row->payload['displayName'],
                'queue'     => '[' . $row->connection . '] ' . $row->queue,
                'failed_at' => $row->failed_at,
            ];
        }

        return [
            'id'           => $row->id,
            'uuid'         => $row->payload['uuid'],
            'name'         => $row->payload['displayName'],
            'queue'        => '[' . $row->connection . '] ' . $row->queue,
            'available_at' => $row->available_at,
        ];
    }

    public function headers(): array
    {
        if ($this->failed) {
            return [
                'uuid'      => __('UUID'),
                'queue'     => __('Queue'),
                'name'      => __('Name'),
                'failed_at' => __('Failed At'),
            ];
        }

        return [
            'uuid'         => __('UUID'),
            'queue'        => __('Queue'),
            'name'         => __('Name'),
            'available_at' => __('Available At'),
        ];
    }

    public function renderCasts(): array
    {
        return [
            'failed_at'    => FormatDateTime::class,
            'available_at' => FormatDateTime::class,
        ];
    }

    public function actions($item): array
    {
        if ($this->failed) {
            return [
                [
                    'type'        => 'livewire',
                    'action'      => 'trace',
                    'parameters'  => $item['uuid'],
                    'text'        => __('Trace'),
                    'actionClass' => '',
                    'iconClass'   => 'fas fa-bug',
                ],
                [
                    'type'        => 'livewire',
                    'action'      => 'retry',
                    'parameters'  => $item['uuid'],
                    'text'        => __('Retry'),
                    'actionClass' => '',
                    'iconClass'   => 'fas fa-sync',
                ],
            ];
        }

        return [
            [
                'type'        => 'livewire',
                'action'      => 'runNow',
                'parameters'  => $item['id'],
                'text'        => __('Run now'),
                'actionClass' => '',
                'iconClass'   => 'fas fa-play',
            ],
            [
                'type'        => 'livewire',
                'action'      => 'stop',
                'parameters'  => $item['id'],
                'text'        => __('Delete'),
                'actionClass' => '',
                'iconClass'   => 'fas fa-trash',
            ],
        ];
    }

    public function runNow(int $jobId): void
    {
        Gate::authorize('is-system-admin');
        $job = Job::find($jobId);
        if (!$job) {
            return;
        }

        $command = unserialize($job->payload['data']['command']);
        app()->call([$command, 'handle']);
        $job->delete();
        alert()->success(__('Job executed'))->now();
    }

    public function stop($job_id)
    {
        Gate::authorize('is-system-admin');
        Job::find($job_id)->delete();
        alert()->success(__('Job deleted'))->now();
    }

    public function trace($job_uuid)
    {
        Gate::authorize('is-system-admin');
        $this->dispatch('openModal', 'job.trace', __('Trace'), ['job_uuid' => $job_uuid]);
    }

    public function retry($job_uuid)
    {
        Gate::authorize('is-system-admin');
        Artisan::call('queue:retry', ['id' => [$job_uuid]]);
        alert()->success(__('Job queued for retry'))->now();
    }
}
