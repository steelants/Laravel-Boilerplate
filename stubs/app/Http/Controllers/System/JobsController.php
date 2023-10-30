<?php

namespace App\Http\Controllers\System;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class JobsController extends Controller
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

        redirect()->route('system.jobs.index')->with('success',  __('boilerplate::ui.jobs-cleared'));
    }
}
