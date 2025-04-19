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

    public function add(string $title, array $options = []): Collection|MenuItem
    {
        if (!isset($this->items)) {
            $this->items = new Collection();
        }

        $item = $this->builder::createMenuItem($title, $options);
        $item->setBuilder($this->builder);
        $this->items->push($item);

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

    public function items(): Collection|null
    {
        return isset($this->items) ? $this->items : null;
    }

	public function type() {
		return $this->type;
	}

	public function isUse(): bool
    {
        return false;
    }

    public function isActive(): bool
    {
        return false;
    }
}
