<?php

use Illuminate\Support\Facades\DB;
use SteelAnts\LaravelBoilerplate\Models\Job;

beforeEach(function () {
    DB::beginTransaction();
});

afterEach(function () {
    DB::rollBack();
});

function insertJob(array $overrides = []): Job
{
    $id = DB::table('jobs')->insertGetId(array_merge([
        'queue'        => 'default',
        'payload'      => json_encode(['uuid' => 'uuid-1', 'displayName' => 'App\\Jobs\\Example']),
        'attempts'     => 0,
        'reserved_at'  => null,
        'available_at' => now()->getTimestamp(),
        'created_at'   => now()->getTimestamp(),
    ], $overrides));

    return Job::findOrFail($id);
}

describe('Job model queue state', function () {
    it('reports a job nobody picked up as neither reserved nor delayed', function () {
        $job = insertJob();

        expect($job->isReserved())->toBeFalse()
            ->and($job->isDelayed())->toBeFalse();
    });

    it('reports a job with reserved_at as reserved', function () {
        $job = insertJob(['reserved_at' => now()->getTimestamp()]);

        expect($job->isReserved())->toBeTrue()
            ->and($job->isDelayed())->toBeFalse();
    });

    it('reports a job scheduled for later as delayed', function () {
        $job = insertJob(['available_at' => now()->addHour()->getTimestamp()]);

        expect($job->isDelayed())->toBeTrue()
            ->and($job->isReserved())->toBeFalse();
    });

    it('does not report a reserved job as delayed even when available_at is in the future', function () {
        $job = insertJob([
            'reserved_at'  => now()->getTimestamp(),
            'available_at' => now()->addHour()->getTimestamp(),
        ]);

        expect($job->isReserved())->toBeTrue()
            ->and($job->isDelayed())->toBeFalse();
    });

    it('casts the unix timestamp columns to Carbon instances', function () {
        $job = insertJob(['reserved_at' => now()->getTimestamp()]);

        expect($job->reserved_at)->toBeInstanceOf(Carbon\Carbon::class)
            ->and($job->available_at)->toBeInstanceOf(Carbon\Carbon::class)
            ->and($job->created_at)->toBeInstanceOf(Carbon\Carbon::class);
    });

    it('does not try to maintain timestamps the jobs table cannot store', function () {
        $job = insertJob();

        expect($job->usesTimestamps())->toBeFalse();
    });
});
