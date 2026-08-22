<?php

use Illuminate\Support\Facades\DB;
use SteelAnts\LaravelBoilerplate\Models\FailedJob;
use SteelAnts\LaravelBoilerplate\Models\Job;

beforeEach(function () {
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

/**
 * Mirrors App\Livewire\Job\DataTable::wrapColumn() - the prefab lives in the App namespace
 * so it is not autoloaded here, but the generated SQL is the part that can break per driver.
 */
function wrapColumn(string $column, string $model = Job::class): string
{
    return $model::query()->getQuery()->getGrammar()->wrap($column);
}

function queueJob(string $uuid, string $displayName): void
{
    DB::table('jobs')->insert([
        'queue'        => 'default',
        'payload'      => json_encode(['uuid' => $uuid, 'displayName' => $displayName]),
        'attempts'     => 0,
        'available_at' => now()->getTimestamp(),
        'created_at'   => now()->getTimestamp(),
    ]);
}

describe('ordering the jobs table by payload columns', function () {
    it('builds a JSON selector rather than a bare column name', function () {
        expect(wrapColumn('payload->uuid'))->toContain('json_extract');
    });

    it('orders waiting jobs by the uuid stored in the payload', function () {
        queueJob('uuid-c', 'C');
        queueJob('uuid-a', 'A');
        queueJob('uuid-b', 'B');

        $ordered = Job::query()
            ->orderByRaw(wrapColumn('payload->uuid') . ' ASC')
            ->get()
            ->map(fn (Job $job) => $job->payload['uuid'])
            ->all();

        expect($ordered)->toBe(['uuid-a', 'uuid-b', 'uuid-c']);
    });

    it('orders waiting jobs by the display name stored in the payload', function () {
        queueJob('uuid-1', 'App\\Jobs\\Zebra');
        queueJob('uuid-2', 'App\\Jobs\\Alpha');

        $ordered = Job::query()
            ->orderByRaw(wrapColumn('payload->displayName') . ' DESC')
            ->get()
            ->map(fn (Job $job) => $job->payload['displayName'])
            ->all();

        expect($ordered)->toBe(['App\\Jobs\\Zebra', 'App\\Jobs\\Alpha']);
    });

    it('orders failed jobs by their real uuid column', function () {
        foreach (['uuid-b', 'uuid-a'] as $uuid) {
            DB::table('failed_jobs')->insert([
                'uuid'       => $uuid,
                'connection' => 'database',
                'queue'      => 'default',
                'payload'    => json_encode(['uuid' => $uuid, 'displayName' => 'App\\Jobs\\Example']),
                'exception'  => 'boom',
                'failed_at'  => now(),
            ]);
        }

        $ordered = FailedJob::query()
            ->orderByRaw(wrapColumn('uuid', FailedJob::class) . ' ASC')
            ->pluck('uuid')
            ->all();

        expect($ordered)->toBe(['uuid-a', 'uuid-b']);
    });
});
