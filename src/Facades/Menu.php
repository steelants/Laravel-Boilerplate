<?php

namespace SteelAnts\LaravelBoilerplate\Facades;

use Illuminate\Support\Facades\Facade;
use SteelAnts\LaravelBoilerplate\Support\MenuCollector;

class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuCollector::class;
    }
}
