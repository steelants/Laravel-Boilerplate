<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class MenuItem
{
    public function __construct(public string $title, public string $id, public string $route, public string $icon, public array $parameters = [])
    {
        if (!Route::has($route)) {
            throw new Exception("route with name: " . $route . " dont exists!", 1);
        }
    }

    public function isUse(): bool
    {
        return Request::is(trim(route($this->route, [], false), '/').'*');
    }

    public function isActive(): bool
    {
        return request()->routeIs($this->route);
    }
}
