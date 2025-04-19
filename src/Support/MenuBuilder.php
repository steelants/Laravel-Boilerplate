<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuBuilder
{
    protected Collection $menuItems;

    public function __construct()
    {
        $this->menuItems = new Collection();
    }

    public function add(string $title, array $options = []): Collection|MenuItem
    {
        $item = $this::createMenuItem($title, $options);
        $item->setBuilder($this);
        $this->menuItems->push($item);

        return $item;
    }

    public function find($index)
    {
        return $this->menuItems->firstWhere('id', '=', $index);
    }

    public function items()
    {
        return $this->menuItems;
    }

    public static function createMenuItem(string $title, array $options = [])
    {
        if (isset($options['route'])) {
            $item = new MenuItemLink($title, ...$options);
            return $item;
        }

        if (isset($options['action'])) {
            $item = new MenuItemAction($title, ...$options);
            return $item;
        }

        $item = new MenuItem($title, ...$options);
        return $item;
    }

	public function addItem(string $title, string $id, string $icon = '', array $parameters = [], array $options = [])
	{
		return $this->add($title, [
			'id' => $id,
			'icon' => $icon,
			'parameters' => $parameters,
			'options' => $options,
		]);
	}

	public function addRoute(string $title, string $id, string $route, string $icon = '', array $parameters = [], array $options = [])
	{
		return $this->add($title, [
			'id' => $id,
			'route' => $route,
			'icon' => $icon,
			'parameters' => $parameters,
			'options' => $options,
		]);
	}

	public function addAction(string $title, string $id, string $action, string $icon = '', array $parameters = [], array $options = [])
	{
		return $this->add($title, [
			'id' => $id,
			'action' => $action,
			'icon' => $icon,
			'parameters' => $parameters,
			'options' => $options,
		]);
	}
}
