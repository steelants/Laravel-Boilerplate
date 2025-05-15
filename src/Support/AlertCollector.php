<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class AlertCollector
{
	protected Collection $alerts;

	public function __construct()
	{
		$this->alerts = new Collection();
	}

	public function add(string $type = 'info', string $icon = '', string $text)
	{
		$this->alerts->add(new AlertItem(type: $type, icon: $icon, text: $text));
	}

	public function get()
	{
		return $this->alerts;
	}
}
