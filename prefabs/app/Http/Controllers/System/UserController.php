<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class UserController extends BaseController
{
    public function index()
    {
        return view('system.user.index');
    }
}
