<?php

namespace App\Traits;

use App\Models\Activity;

trait Auditable
{
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
