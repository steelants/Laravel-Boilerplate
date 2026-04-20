<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use SteelAnts\LaravelBoilerplate\Helpers\JobHelper;
use SteelAnts\LaravelBoilerplate\Models\FailedJob;
use SteelAnts\LaravelBoilerplate\Models\Job;

class JobsController extends BaseController
{
    public function index()
    {
        $jobClasses = JobHelper::getAllManuallyRunnableJobs();

        return view('system.job.index', [
            'layout'        => config('boilerplate.layouts.system'),
            'waiting_count' => Job::count(),
            'failed_count'  => FailedJob::count(),
            'jobs_classes'  => $jobClasses,
        ]);
    }

    public function clear()
    {
        DB::table('failed_jobs')->delete();

        return redirect()->route('system.jobs.index')->with('success', __('Jobs cleared!'));
    }
}
