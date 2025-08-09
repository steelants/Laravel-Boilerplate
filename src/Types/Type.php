<?php

namespace SteelAnts\LaravelBoilerplate\Types;

abstract class Type
{
    abstract public static function getAll(): array;

    public static function getNames()
    {
        $all = self::getAll();
        return array_map(fn ($val) => __($val), $all);
    }

    public static function getName($type)
    {
        $all = self::getAll();
        if (isset($all[$type])) {
            return __($all[$type]);
        }
        return 'NULL';
    }

    public static function getkeys()
    {
        return array_keys(self::getAll());
    }
}
