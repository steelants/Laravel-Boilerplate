<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JobsController extends BaseController
{
    public function index()
    {
        $failed_jobs = DB::table('failed_jobs')->select()->get();
        $jobs = DB::table('jobs')->select()->get();


        return view('system.jobs.index', [
            'failed_jobs' => $failed_jobs,
            'jobs' => $jobs,
        ]);
    }

    public function clear()
    {
        DB::table('failed_jobs')->delete();

        return redirect()->route('system.jobs.index')->with('success', __('boilerplate::ui.jobs-cleared'));
    }
}
