<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use App\Models\User;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use SteelAnts\LaravelBoilerplate\Traits\SystemPage;

class UserController extends BaseController
{
    use CRUD, SystemPage;

    public string $model = User::class;
}
