<?php

namespace SteelAnts\LaravelBoilerplate\Facades;

use Illuminate\Support\Facades\Facade;
use SteelAnts\LaravelBoilerplate\Support\AlertCollector;

class Alert extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AlertCollector::class;
    }
}
