<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use SteelAnts\LaravelBoilerplate\Models\Activity;

class ActivityService
{
	public function __construct(private Activity $activity)
	{
	}

	public function log(string $text, ?array $data = null): void
	{
		$this->storeActivity(__($text), $data);
	}

	public function logUrl(string $text = 'Accessed url'): void
	{
		$this->storeActivity(__($text), [
			'url' => url()->full(),
		]);
	}

	private function storeActivity(string $langText, ?array $data = null): void
	{
		$activity = $this->activity->newInstance();
		$activity->lang_text = $langText;
		$activity->data = $data;
		$activity->save();
	}
}
