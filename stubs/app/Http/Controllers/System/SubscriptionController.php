<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('system.subscription.index');
    }
}