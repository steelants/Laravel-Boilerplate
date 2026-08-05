<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use SteelAnts\LaravelBoilerplate\Facades\FileStorage;

/**
 * @deprecated Přesunuto do SteelAnts\LaravelBoilerplate\Support\FileCollector (facade-backed
 * konvence, viz AlertCollector/MenuCollector). Tenhle shim jen drží starý namespace funkční —
 * deleguje na FileStorage facade, ne na vlastní kopii logiky.
 */
class FileService
{
    public static function __callStatic(string $method, array $arguments)
    {
        return FileStorage::$method(...$arguments);
    }
}
