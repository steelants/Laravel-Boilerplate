<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use App\Models\Activity;

class ActivityService
{
	public static function log($text, $data = null)
	{
		$activity = new Activity();
		$activity->lang_text = $text;
		$activity->data = $data;
		$activity->save();
	}

	public static function logUrl($text = 'Accessed url')
	{
		$activity = new Activity();
		$activity->lang_text = $text;
		$activity->data = [
			'url' => url()->full(),
		];
		$activity->save();
	}
}
