<?php

namespace SteelAnts\LaravelBoilerplate\Support;

class AlertItem
{
	public function __construct(public string $type = 'info', public string $text, public string $icon = '', public bool $persist = false, public array $data = [])
    {
    }
}
