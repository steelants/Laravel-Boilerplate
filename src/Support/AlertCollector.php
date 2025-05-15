<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Facades\Session;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

class AlertCollector
{
	public function add(string $type = 'info', string $text, string $icon = '', int $mode = AlertModeType::RELOAD)
	{
		$alerts = Session::get('alerts-' . $mode, []);
		$alerts[] = new AlertItem(type: $type, text: $text, icon: $icon);
		if ($mode == AlertModeType::RELOAD) {
			Session::flash('alerts-'. $mode, $alerts);
		} else if ($mode == AlertModeType::INSTANT) {
			Session::now('alerts-'. $mode, $alerts);
		}
	}

	public function get(int $mode = AlertModeType::RELOAD)
	{
		$alerts = Session::get('alerts-' . $mode, []);
		$limitedAlerts = array_slice($alerts, 0, 5); // limit
		$restAlerts = array_slice($alerts, 5);
		if (count($restAlerts) > 0) {
			Session::flash('alerts-'. $mode, $restAlerts);
		}
		return $limitedAlerts;
	}
}
