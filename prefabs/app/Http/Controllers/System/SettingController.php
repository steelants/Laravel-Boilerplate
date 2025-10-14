<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class SettingController extends BaseController
{
	public function index()
	{
		return view('system.setting.index', [
			'layout' => config('boilerplate.layouts.system'),
		]);
	}
}
