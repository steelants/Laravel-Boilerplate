<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSettings
{
    public function settings() : MorphMany
    {
        return $this->morphMany(Setting::class, 'settable');
    }
}
