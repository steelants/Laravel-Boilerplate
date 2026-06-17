<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Illuminate\Support\Collection;

class SettingResolver
{
    public static function resolve(?Collection $matches, string $key, $default = null)
    {
        if ($matches === null || $matches->isEmpty()) {
            return $default ?? config("setting_field.{$key}.value");
        }

        if ($matches->count() === 1) {
            return $matches->first()->value;
        }

        return $matches->pluck('value', 'index')->toArray();
    }
}
