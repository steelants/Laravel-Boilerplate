<?php

namespace App\Http\Controllers\System;

use App\Models\User;
use SteelAnts\LaravelBoilerplate\Http\Controllers\CrudController;

class UserController extends CrudController
{
    public Model $model = User::class;
}
