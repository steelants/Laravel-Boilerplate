<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use SteelAnts\LaravelBoilerplate\Models\Setting;
use SteelAnts\LaravelBoilerplate\Support\SettingResolver;

trait HasSettings
{
    public function settings(): MorphMany
    {
        return $this->morphMany(config('boilerplate.models.setting', Setting::class), 'settable');
    }

    public function getSettings($key, $default = null)
    {
        return SettingResolver::resolve(
            $this->settings->where('index', $key),
            $key,
            $default,
        );
    }
}
