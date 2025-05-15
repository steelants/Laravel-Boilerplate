<?php

namespace SteelAnts\LaravelBoilerplate\Types;

class AlertModeType
{
    const RELOAD = 0; //is viewed once after reload page
    const INSTANT = 1; //is viewed instant thanks to livewire

    public static function getNames()
    {
        return $names = [
            self::RELOAD     => 'Reload',
            self::INSTANT => 'Instant',
        ];
    }

    public static function getName($type)
    {
        $names = self::getNames();
        if (isset($names[$type])) {
            return __($names[$type]);
        }
        return 'NULL';
    }

    public static function getkeys()
    {
        return array_keys(self::getNames());
    }
}
