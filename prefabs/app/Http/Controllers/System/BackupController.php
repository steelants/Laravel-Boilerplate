<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    public function index()
    {
        return view('system.backup.index', []);
    }
}
