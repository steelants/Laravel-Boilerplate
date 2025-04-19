<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuCollector
{
    protected Collection $menus;

    public function __construct()
    {
        $this->menus = new Collection();
    }

    public function make(string $menuKey = '', callable $callback = null)
    {
		$menu = new MenuBuilder();

		if (is_callable($callback)) {
            call_user_func($callback, $menu);
        }

		if (!empty($menuKey)) {
			$this->menus->put($menuKey, $menu);
		}

		return $menu;
    }

    public function get(string $menuKey)
    {
        return $this->menus->get($menuKey);
    }
}
