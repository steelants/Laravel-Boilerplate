<?php

namespace App\Helpers;

class AbstractHelper
{
    public static function classes_in_namespace($namespace)
    {
        $namespace .= '\\';
        $myClasses  = array_filter(get_declared_classes(), function ($item) use ($namespace) {
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
            $filename = $path . '/' . $result;
            if (is_dir($filename)) {
                $out = array_merge($out, self::getClassNames($filename));
            } else {
                $classFilePath = explode('/', $filename);
                $out[] = substr($classFilePath[count($classFilePath) - 1], 0, -4);
            }
        }


        return $out;
    }
}
