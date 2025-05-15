<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

class AlertCollector
{
	public function add(string $type = 'info', string $text, string $icon = '', int $mode = AlertModeType::RELOAD)
	{
		$alerts = session('alerts-' . $mode, []);
		$alerts[] = new AlertItem(type: $type, text: $text, icon: $icon);
		if ($mode == AlertModeType::RELOAD) {
			session()->flash('alerts-'. $mode, $alerts);
		} else if ($mode == AlertModeType::INSTANT) {
			session()->now('alerts-'. $mode, $alerts);
		}
	}

	public function get(int $mode = AlertModeType::RELOAD)
	{
		return session('alerts-' . $mode, []);
	}
}
