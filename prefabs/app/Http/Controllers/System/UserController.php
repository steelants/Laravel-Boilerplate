<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;

class UserController extends BaseController
{
    use CRUD;
    public string $model = "user";
}
