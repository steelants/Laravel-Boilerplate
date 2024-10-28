<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public function activities() : MorphMany
    {
        return $this->morphMany(Activity::class, 'actor');
    }

    public static function bootAuditable()
    {
        if (app()->runningInConsole()) {
            return;
        }

        static::created(function ($model) {
            $activity = new Activity();
            $activity->lang_text = __('boilerplate::ui.created', ["model" => class_basename($model) . " " . $model->name]);
            $activity->affected()->associate($model);
            $activity->save();
        });

        static::updating(function ($model) {
            $activity = new Activity();
            $activity->lang_text = __('boilerplate::ui.updated', ["model" => class_basename($model) . " " . $model->name]);
            $activity->affected()->associate($model);
            $activity->save();
        });

        static::deleting(function ($model) {
            $activity = new Activity();
            $activity->lang_text = __('boilerplate::ui.deleted', ["model" => class_basename($model) . " " . $model->name]);
            $activity->save();
        });
    }
}
