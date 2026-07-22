<?php

namespace SteelAnts\LaravelBoilerplate\Facades;

use Illuminate\Support\Facades\Facade;
use SteelAnts\LaravelBoilerplate\Support\FileCollector;

class FileStorage extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FileCollector::class;
    }
}
