<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Models\Activity;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use SteelAnts\LaravelBoilerplate\Traits\SystemPage;

class AuditController extends BaseController
{
	use CRUD, SystemPage;
	public string $model = Activity::class;
}
