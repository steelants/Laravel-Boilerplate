<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class AuditController extends BaseController
{
    public function index()
    {
        return view('system.audit.index');
    }
}
