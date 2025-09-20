<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Models\Activity;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use SteelAnts\LaravelBoilerplate\Traits\HasSystemLayout;

class AuditController extends BaseController
{
	use CRUD;
	public string $model = Activity::class;

	public function __construct() {
		$this->layout = config('boilerplate.layouts.system');
	}
}
