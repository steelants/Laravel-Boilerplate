<?php

namespace App\Traits;

use App\Models\Activity;
use App\Models\User;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $activity = new Activity();
            if ($model instanceof User) {
                $activity->lang_text = __('boilerplate::ui.created', ["model" => "Uživatel " . $model->name]);
                $activity->user_id = $model->id;
            }
            $activity->save();
        });

        static::updating(function ($model) {
            $activity = new Activity();
            if ($model instanceof User) {
                $activity->lang_text = __('boilerplate::ui.updated', ["model" => "Uživatel " . $model->name]);
                $activity->user_id = $model->id;
            }
            $activity->save();
        });

        static::deleting(function ($model) {
            $activity = new Activity();
            if ($model instanceof User) {
                $activity->lang_text = __('boilerplate::ui.deleted', ["model" => "Uživatel " . $model->name]);
                $activity->user_id = null;
            }
            $activity->save();
        });
    }
}