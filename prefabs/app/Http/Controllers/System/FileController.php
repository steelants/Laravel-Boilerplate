<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use SteelAnts\LaravelBoilerplate\Traits\SystemPage;

class FileController extends BaseController
{
	use CRUD, SystemPage;
	public array $views = [
		'index' => 'system.file.index'
	];
	public string $model = File::class;
}
