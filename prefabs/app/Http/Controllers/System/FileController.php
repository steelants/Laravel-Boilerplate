<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class FileController extends BaseController
{
	public function index() {
		return view('system.file.index');
	}
}
