<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Support\Facades\Route;

class MenuItem
{
    public function __construct(public string $title, public string $id, public string $route)
    {
        if (!Route::has($route)) {
            throw new Exception("route with name: " . $route . " dont exists!", 1);
        }
    }

    public function isActive(): bool
    {
        request()->routeIs($this->route);
    }
}
