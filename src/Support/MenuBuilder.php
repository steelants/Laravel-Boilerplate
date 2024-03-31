<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuBuilder
{
    protected $menuItems;

    public function __construct() {
        $this->menuItems = new Collection;
    }

    public function add($title, $options = '')
	{
		$item = new MenuItem($title, ...$options);
		$this->menuItems->push($item);

		return $this;
	}
}
