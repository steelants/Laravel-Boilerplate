<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class MenuItemLink extends MenuItem
{
	protected string $type = 'route';

    public function __construct(public string $title, public string $id, public string $route, public string $icon = '', public array $parameters = [], public array $options = [])
    {
        if (!Route::has($route)) {
            throw new Exception("route with name: " . $route . " dont exists!", 1);
        }
    }

    public function isUse(): bool
    {
        return Request::is(trim(route($this->route, $this->parameters, false), '/').'*');
    }

    public function isActive(): bool
    {
		$fullUrl = request()->path();
		if (preg_match('/livewire/', $fullUrl)) {
			$fullUrl = request()->headers->get('referer');
		}

		if(empty($this->parameters)){
			$data = explode(".", $this->route);
			return (preg_match("/{$data[0]}/" , $fullUrl) && (empty($data[1]) || $data[1] == "index")) || rtrim($fullUrl) == rtrim(route($this->route), '/');
		}
		return rtrim($fullUrl) == rtrim(route($this->route, $this->parameters), '/');
    }
}
