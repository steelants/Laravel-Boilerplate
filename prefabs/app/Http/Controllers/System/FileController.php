<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;

class FileController extends BaseController
{
	use CRUD;
	public array $views = [
		'index' => 'system.file.index'
	];
	public string $model = File::class;

	public function __construct() {
		$this->layout = config('boilerplate.layouts.system');
	}
}
