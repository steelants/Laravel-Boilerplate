<?php

namespace SteelAnts\LaravelBoilerplate\Facades;

use Illuminate\Support\Facades\Facade;

class Alert extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'alert';
    }
}
