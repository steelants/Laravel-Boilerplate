<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use SteelAnts\LaravelBoilerplate\Models\Setting;

trait HasSettings
{
    public function settings(): MorphMany
    {
        return $this->morphMany(config('boilerplate.models.setting', Setting::class), 'settable');
    }

    public function getSettings($key, $default = null)
    {
        $value = $this->settings()->where('index', $key)->get();
        if ($value->isEmpty()) {
            return $default;
        }

        if ($value->count() == 1) {
            return $value->first()->value;
        }

        return $value->pluck('value', 'index')->toArray();
    }
}
