<?php

namespace SteelAnts\LaravelBoilerplate\Helpers;

use ReflectionClass;
use SteelAnts\LaravelBoilerplate\Attributes\AllowManualRun;

class JobHelper
{
    public static function getAllManuallyRunnableJobs(): array
    {
        $out = [];
        foreach (config('boilerplate.jobs.namespaces', [
            'App\\Jobs\\',
            'SteelAnts\\LaravelBoilerplate\\Jobs\\',
            'SteelAnts\\LaravelBoilerplate\\Dashboard\\Jobs\\',
        ]) as $namespace) {
            $out = array_merge($out, static::findManuallyRunnableInNamespace($namespace));
        }

        return $out;
    }

    public static function getManuallyRunnableJobs(string $path): array
    {
        $out = [];
        if (!is_dir($path)) {
            return $out;
        }

        foreach (scandir($path) as $result) {
            if ($result === '.' || $result === '..') {
                continue;
            }
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, static::getManuallyRunnableJobs($filename));

                continue;
            }

            $contents = file_get_contents($filename);
            if (
                preg_match('/namespace\s+(.+?);/', $contents, $nsMatch) &&
                preg_match('/class\s+(\w+)/', $contents, $classMatch)
            ) {
                $fqcn = trim($nsMatch[1]) . '\\' . trim($classMatch[1]);
                if (!class_exists($fqcn)) {
                    require_once $filename;
                }
                if (class_exists($fqcn) && !empty((new ReflectionClass($fqcn))->getAttributes(AllowManualRun::class))) {
                    $out[] = trim($classMatch[1]);
                }
            }
        }

        return $out;
    }

    public static function resolveClass(string $name): string
    {
        $namespaces = config('boilerplate.jobs.namespaces', ['App\\Jobs\\']);
        foreach ($namespaces as $namespace) {
            $fqcn = $namespace . $name;
            if (class_exists($fqcn)) {
                return $fqcn;
            }
        }

        return $namespaces[0] . $name;
    }

    public static function hasAllowManualRun(string $fqcn): bool
    {
        if (!class_exists($fqcn)) {
            return false;
        }

        return !empty((new ReflectionClass($fqcn))->getAttributes(AllowManualRun::class));
    }

    private static function findManuallyRunnableInNamespace(string $namespace): array
    {
        // Production: classmap populated by composer dump-autoload --optimize
        $classes = static::getClassesFromClassmap($namespace);
        if (!empty($classes)) {
            return array_values(array_filter(
                array_map(fn($fqcn) => substr($fqcn, strrpos($fqcn, '\\') + 1), $classes),
                fn($short) => static::hasAllowManualRun($namespace . $short),
            ));
        }

        // Dev fallback: derive directories from Composer PSR-4 map and scan
        $out = [];
        foreach (static::getDirectoriesForNamespace($namespace) as $dir) {
            $out = array_merge($out, static::getManuallyRunnableJobs($dir));
        }

        return $out;
    }

    private static function getClassesFromClassmap(string $namespace): array
    {
        $loader = require base_path('vendor/autoload.php');

        return array_values(array_filter(
            array_keys($loader->getClassMap()),
            fn($class) => str_starts_with($class, $namespace),
        ));
    }

    private static function getDirectoriesForNamespace(string $namespace): array
    {
        $loader = require base_path('vendor/autoload.php');
        $dirs = [];

        foreach ($loader->getPrefixesPsr4() as $prefix => $baseDirs) {
            if (!str_starts_with($namespace, $prefix)) {
                continue;
            }
            $subPath = str_replace('\\', '/', substr($namespace, strlen($prefix)));
            foreach ($baseDirs as $baseDir) {
                $dir = rtrim($baseDir, '/') . '/' . trim($subPath, '/');
                if (is_dir($dir)) {
                    $dirs[] = $dir;
                }
            }
        }

        return $dirs;
    }
}
