<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Models\Subscription;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use SteelAnts\LaravelBoilerplate\Traits\SystemPage;

class SubscriptionController extends BaseController
{
	use CRUD, SystemPage;
	public string $model = Subscription::class;

	public function __construct() {
		$this->layout = config('boilerplate.layouts.system');
	}
}
