<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Support\Facades\Route;

class MenuItem
{
    public function __construct(public $title, public $id, public $route)
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
