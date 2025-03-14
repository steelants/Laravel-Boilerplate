<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuItem
{
    private MenuBuilder $builder;
    private Collection $items;

    public function __construct(public string $title, public string $id, public string $icon, public array $parameters = [])
    {
    }

    public function setBuilder(MenuBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function add(string $title, array $options = []): Collection
    {
        if (!isset($this->items)) {
            $this->items = new Collection();
        }

        $item = $this->builder::createMenuItem($title, $options);
        $item->setBuilder($this->builder);
        $this->items->push($item);

        return $item;
    }

    public function items(): Collection|bool
    {
        return isset($this->items) ? $this->items : false;
    }
}
