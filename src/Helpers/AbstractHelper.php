<?php

namespace SteelAnts\LaravelBoilerplate\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class AbstractHelper
{
    public static function getClassesInNamespace($namespace)
    {
        $namespace .= '\\';
        $myClasses = array_filter(get_declared_classes(), function ($item) use ($namespace) {
            return substr($item, 0, strlen($namespace)) === $namespace;
        });

        $theClasses = [];
        foreach ($myClasses as $class) {
            $theParts = explode('\\', $class);
            $theClasses[] = end($theParts);
        }

        return $theClasses;
    }

    public static function getClassNames($path)
    {
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') {
                continue;
            }
            $filename = $path.'/'.$result;
            if (is_dir($filename)) {
                $out = array_merge($out, self::getClassNames($filename));
            } else {
                $classFilePath = explode('/', $filename);
                $out[] = substr($classFilePath[count($classFilePath) - 1], 0, -4);
            }
        }

        return $out;
    }

    public static function findFiles(array $patterns, array $except = []): array
    {
        $files = [];

        foreach ($patterns as $pattern) {
            foreach (glob($pattern) as $file) {
                $contents = file_get_contents($file);

                if (
                    preg_match('/namespace\s+(.+?);/', $contents, $nsMatch) &&
                    preg_match('/class\s+(\w+)/', $contents, $classMatch)
                ) {
                    $namespace = trim($nsMatch[1]);
                    $className = trim($classMatch[1]);
                    $fqcn = $namespace.'\\'.$className;

                    if (! empty($except) && count($except) > 0 && in_array($fqcn, $except)) {
                        continue;
                    }

                    if (! class_exists($fqcn)) {
                        require_once $file;
                    }

                    if (
                        class_exists($fqcn) &&
                        is_subclass_of($fqcn, Model::class) &&
                        ! (new ReflectionClass($fqcn))->isAbstract()
                    ) {
                        $files[] = $fqcn;
                    }
                }
            }
        }

        return $files;
    }

    public static function namespaceToPath(string $namespace): string
    {
        // mechanicky: App\ → app/ a \ → /
        $ns = ltrim($namespace, '\\'); // odstraň úvodní backslash
        if (str_starts_with($ns, 'App\\')) {
            $rel = substr($ns, strlen('App\\')); // zahodí "App\"
            $rel = str_replace('\\', DIRECTORY_SEPARATOR, $rel);

            return base_path('app'.DIRECTORY_SEPARATOR.$rel);
        }

        // když není App\, jen nahraď backslashe a vrať pod base_path
        return base_path(str_replace('\\', DIRECTORY_SEPARATOR, $ns));
    }
}
