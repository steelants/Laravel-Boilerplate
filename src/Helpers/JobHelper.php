<?php

namespace SteelAnts\LaravelBoilerplate\Helpers;

use ReflectionClass;
use SteelAnts\LaravelBoilerplate\Attributes\AllowManualRun;

class JobHelper
{
    protected static array $extraPaths = [];

    protected static array $namespaces = [
        'App\\Jobs\\',
        'SteelAnts\\LaravelBoilerplate\\Jobs\\',
        'SteelAnts\\LaravelBoilerplate\\Dashboard\\Jobs\\',
    ];

    public static function registerPath(string $path): void
    {
        static::$extraPaths[] = $path;
    }

    public static function registerNamespace(string $namespace): void
    {
        static::$namespaces[] = rtrim($namespace, '\\') . '\\';
    }

    private static function defaultPaths(): array
    {
        $paths = array_merge(
            [
                app_path('Jobs'),
                base_path('packages/Laravel-Boilerplate/src/Jobs'),
                base_path('packages/Laravel-Boilerplate.Dashboard/src/Jobs'),
                base_path('vendor/steelants/laravel-boilerplate/src/Jobs'),
                base_path('vendor/steelants/laravel-boilerplate.dashboard/src/Jobs'),
            ],
            static::$extraPaths,
        );

        return array_filter($paths, 'is_dir');
    }

    public static function getAllManuallyRunnableJobs(): array
    {
        $out = [];
        foreach (static::defaultPaths() as $path) {
            $out = array_merge($out, static::getManuallyRunnableJobs($path));
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
        foreach (static::$namespaces as $namespace) {
            $fqcn = $namespace . $name;
            if (class_exists($fqcn)) {
                return $fqcn;
            }
        }

        return static::$namespaces[0] . $name;
    }

    public static function hasAllowManualRun(string $fqcn): bool
    {
        if (!class_exists($fqcn)) {
            return false;
        }

        return !empty((new ReflectionClass($fqcn))->getAttributes(AllowManualRun::class));
    }
}
