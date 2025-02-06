<?php

namespace App\Http\Controllers\System;

use App\Models\User;
use SteelAnts\LaravelBoilerplate\Controllers\Http\CrudController;

class UserController extends CrudController
{
    public string $model = "user";
}
