<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuCollector
{
    protected $menus;

    public function __construct() {
        $this->menus = new Collection;
    }

    public function make($menuKey, $callback){
        if(is_callable($callback))
		{
			$menu = new MenuBuilder($menuKey);
			call_user_func($callback, $menu);
			$this->menus->put($menuKey, $menu);
			return $menu;
		}
    }

    public function get($menuKey)
    {
        return $this->menus->get($menuKey);
    }
}
