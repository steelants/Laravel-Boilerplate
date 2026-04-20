<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
}
