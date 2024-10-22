<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use App\Models\Activity;
use Illuminate\Support\Str;

class AuditController extends BaseController
{
    public function index()
    {
        return view('system.audit.index');
    }
}
