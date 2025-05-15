<?php

namespace SteelAnts\LaravelBoilerplate\Support;

class AlertItem
{
	public function __construct(public string $type = 'info', public string $icon = '', public string $text, public array $data = [])
    {
    }
}
