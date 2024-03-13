<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class BackupController extends BaseController
{
    public function index()
    {
        return view('system.backup.index', []);
    }
}
