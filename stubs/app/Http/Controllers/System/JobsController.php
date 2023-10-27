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
}
