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

    public function add(string $title, array $options = [])
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
}
