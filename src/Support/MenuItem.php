<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class MenuItem
{
    private MenuBuilder $builder;
    private Collection $items;
	protected string $type = 'item';

    public function __construct(public string $title, public string $id, public string $icon = '', public array $parameters = [], public array $options = [])
    {
    }

    public function setBuilder(MenuBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function add(string $title, array $options = []): MenuItem
    {
        if (!isset($this->items)) {
            $this->items = new Collection();
        }

        $item = $this->builder::createMenuItem($title, $options);
        $item->setBuilder($this->builder);
        $this->items->push($item);

        return $item;
    }

	public function addItem($item){
		if (!isset($this->items)) {
            $this->items = new Collection();
        }

		$item->setBuilder($this->builder);
        $this->items->push($item);

        return $item;
	}

    public function items(): Collection|bool
    {
        return isset($this->items) ? $this->items : false;
    }

	public function type() {
		return $this->type;
	}
}
