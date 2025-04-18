<?php

namespace SteelAnts\LaravelBoilerplate\Support;

class MenuItemAction extends MenuItem
{
	protected string $type = 'action';

    public function __construct(public string $title, public string $id, public string $action, public string $icon = '', public array $parameters = [], public array $options = [])
    {
    }
}
