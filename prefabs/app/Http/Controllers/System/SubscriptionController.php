<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class SubscriptionController extends BaseController
{
    public function index()
    {
        return view('system.subscription.index');
    }
}