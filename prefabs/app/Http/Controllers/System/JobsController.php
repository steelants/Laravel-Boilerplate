<?php

namespace App\Http\Controllers\System;

use SteelAnts\LaravelBoilerplate\Helpers\AbstractHelper;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class JobsController extends BaseController
{
	public function index()
	{

		$job_actions = [];
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
			$job_actions += $rules;
		}

		$failed_jobs = DB::table('failed_jobs')->select(['id', 'uuid', 'queue', 'exception', 'failed_at'])->selectRaw('SUBSTRING(payload, 1, 150) AS payload')->get();
		$jobs = DB::table('jobs')->select(['id', 'queue', 'available_at'])->selectRaw('SUBSTRING(payload, 1, 150) AS payload')->get();

		return view('system.job.index', [
			'jobs_classes' => $rules,
			'failed_jobs'  => $failed_jobs,
			'jobs'         => $jobs,
			'job_actions'  => $job_actions,
		]);
	}

	public function clear()
	{
		Gate::authorize('is-admin');

		DB::table('failed_jobs')->delete();

		return redirect()->route('system.jobs.index')->with('success', __('boilerplate::ui.jobs-cleared'));
	}

	public function call(Request $request, $job)
	{
		Gate::authorize('is-admin');

		$class = '\\App\\Jobs\\' . $job;

		dispatch(new $class());
		return redirect()->route('system.job.index')->with('success', __('Job přidán do fronty'));
	}
}
