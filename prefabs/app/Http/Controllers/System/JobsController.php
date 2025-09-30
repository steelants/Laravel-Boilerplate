<?php

namespace App\Http\Controllers\System;

use SteelAnts\LaravelBoilerplate\Helpers\AbstractHelper;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use SteelAnts\LaravelBoilerplate\Models\FailedJob;
use SteelAnts\LaravelBoilerplate\Models\Job;

class JobsController extends BaseController
{
	public function index()
	{
		$rules = [];
		foreach (
			[
				(app_path() . "/Jobs"),
				(base_path() . "/packages/Laravel-Boilerplate/src/Jobs"),
				(base_path() . "/vendor/steelants/laravel-boilerplate/src/Jobs"),

			] as $path
		) {
			if (!File::exists($path)) {
				continue;
			}
			$rules += AbstractHelper::getClassNames($path);
		}

		return view('system.job.index', [
			'layout' => config('boilerplate.layouts.system'),
			'waiting_count' => Job::count(),
			'failed_count'  => FailedJob::count(),
			'jobs_classes' => $rules,
		]);
	}

	public function clear()
	{
		DB::table('failed_jobs')->delete();
		return redirect()->route('system.jobs.index')->with('success', __('boilerplate::ui.jobs-cleared'));
	}
}
