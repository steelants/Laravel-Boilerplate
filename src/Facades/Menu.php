<?php

namespace SteelAnts\LaravelBoilerplate\Facades;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'menu';
    }
}
