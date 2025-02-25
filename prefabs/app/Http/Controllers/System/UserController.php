<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Traits\CreateReadUpdateDelete;

class UserController extends BaseController
{
    use CreateReadUpdateDelete;
    public string $model = "user";
}
