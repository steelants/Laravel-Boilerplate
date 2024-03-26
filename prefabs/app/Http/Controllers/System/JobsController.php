<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

class JobsController extends BaseController
{
    public function index()
    {
        $jobs = DB::table('failed_jobs')->select()->get();

        return view('system.jobs.index', [
            'items' => $jobs,
        ]);
    }

    public function clear(){
        DB::table('failed_jobs')->delete();

        return redirect()->route('system.jobs.index')->with('success',  __('boilerplate::ui.jobs-cleared'));
    }
}
