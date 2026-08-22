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

        // The jobs table has no connection column - only failed_jobs stores it.
        return [
            'id'           => $row->id,
            'uuid'         => $row->payload['uuid'],
            'name'         => $row->payload['displayName'],
            'queue'        => $row->queue,
            'status'       => match (true) {
                $row->isReserved() => 'processing',
                $row->isDelayed()  => 'delayed',
                default            => 'waiting',
            },
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
            'status'       => __('Status'),
            'available_at' => __('Available At'),
        ];
    }

    /**
     * Tells apart a job a worker already picked up (reserved_at is stamped) from one
     * that is only sitting in the queue, and from one scheduled for later.
     */
    public function renderColumnStatus($value, $row): string
    {
        $badges = [
            'processing' => ['text-bg-primary', __('Processing')],
            'delayed'    => ['text-bg-info', __('Delayed')],
            'waiting'    => ['text-bg-secondary', __('Waiting')],
        ];

        [$class, $label] = $badges[$value] ?? $badges['waiting'];

        return '<span class="badge ' . $class . '">' . e($label) . '</span>';
    }

    /**
     * Status is not a real column, so sorting by it needs the same expression in SQL.
     */
    public function orderColumnStatus(): string
    {
        return '(CASE WHEN reserved_at IS NOT NULL THEN 0 WHEN available_at > '
            . now()->getTimestamp() . ' THEN 2 ELSE 1 END)';
    }

    public function orderColumnUuid(): string
    {
        // failed_jobs has a real uuid column, the jobs table only carries it in the payload.
        return $this->wrapColumn($this->failed ? 'uuid' : 'payload->uuid');
    }

    public function orderColumnName(): string
    {
        return $this->wrapColumn('payload->displayName');
    }

    /**
     * Both tables read uuid and name out of the payload JSON, so ordering by them needs a
     * JSON selector. Grammar::wrap() builds the driver-specific one (json_extract on SQLite,
     * json_unquote(json_extract(...)) on MySQL) instead of hardcoding a single dialect.
     */
    protected function wrapColumn(string $column): string
    {
        return $this->query()->getQuery()->getGrammar()->wrap($column);
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
