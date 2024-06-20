<?php

namespace App\Http\Controllers\System;

use App\Helpers\AbstractHelper;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobsController extends BaseController
{
    public function index()
    {
        $rules = AbstractHelper::getClassNames(app_path() . "/Jobs");

        $job_actions = $rules;
        $failed_jobs = DB::table('failed_jobs')->select()->get();
        $jobs = DB::table('jobs')->select()->get();

        return view('system.jobs.index', [
            'failed_jobs' => $failed_jobs,
            'jobs' => $jobs,
            'job_actions' => $job_actions,
        ]);
    }

    public function clear()
    {
        DB::table('failed_jobs')->delete();

        return redirect()->route('system.jobs.index')->with('success', __('boilerplate::ui.jobs-cleared'));
    }

    public function call($job)
    {
        $class = '\\App\\Jobs\\' . $job;
        dispatch(new $class());
        return redirect()->route('system.jobs.index')->with('success', __('Job přidán do fronty'));
    }
}