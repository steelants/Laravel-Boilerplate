<?php

namespace SteelAnts\LaravelBoilerplate\Helpers;

use Illuminate\Support\Str;
use Livewire\Livewire;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LivewireHelper
{
	public static function registerLivewire($livewirePath = (__DIR__ . '/Livewire'), $namespace = '\\SteelAnts\\LaravelBoilerplate\\', $prefix)
    {
        if (is_dir($livewirePath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($livewirePath));
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($livewirePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $classPath = str_replace('.php', '', $relativePath);
                    $classNamespace = $namespace . str_replace(DIRECTORY_SEPARATOR, '\\', $classPath);
                    $componentName = Str::of($classPath)->replace(DIRECTORY_SEPARATOR, '.')->kebab()->replace('.-', '.')->value;
                    Livewire::component((Str::trim($prefix, '.') . '.'. $componentName), $classNamespace);
                }
            }
        }
    }
}
