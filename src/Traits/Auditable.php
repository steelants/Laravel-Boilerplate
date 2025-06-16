<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'actor');
    }

    public static function bootAuditable()
    {
        if (app()->runningInConsole()) {
            return;
        }

        static::created(function ($model) {
            $this->createdBy($model);
        });

        static::updating(function ($model) {
            $this->updatingBy($model);
        });

        static::deleting(function ($model) {
            $this->deletingBy($model);
        });
    }

	public function createdBy($model){
		$activity = new Activity();
		$activity->lang_text = __('boilerplate::ui.created', ["model" => class_basename($model) . " " . $model->name]);
		$activity->affected()->associate($model);
		$activity->save();
	}

	public function updatingBy($model){
		$activity = new Activity();
		$activity->lang_text = __('boilerplate::ui.updated', ["model" => class_basename($model) . " " . $model->name]);
		$activity->affected()->associate($model);
		$activity->save();
	}

	public function deletingBy($model){
		$activity = new Activity();
        $activity->lang_text = __('boilerplate::ui.deleted', ["model" => class_basename($model) . " " . $model->name]);
        $activity->save();
	}
}
