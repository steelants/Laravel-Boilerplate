<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use SteelAnts\LaravelBoilerplate\Models\Setting;

trait HasSettings
{
    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'settable');
    }
}
