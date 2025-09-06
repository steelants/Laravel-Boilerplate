<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use App\Models\User;

class UserController extends BaseController
{
    use CRUD;
    public string $model = User::class;
}
