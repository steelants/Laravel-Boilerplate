<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
	protected static $nameColumn = "name";

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
            self::createdBy($model);
        });

        static::updating(function ($model) {
            self::updatingBy($model);
        });

        static::deleting(function ($model) {
            self::deletingBy($model);
        });
    }

	protected static function createdBy($model){
		$activity = new Activity();
		$activity->lang_text = __('boilerplate::ui.created', ["model" => class_basename($model) . " " . $model->{self::$nameColumn}]);
		$activity->affected()->associate($model);
		$activity->save();
	}

	protected static function updatingBy($model){
		$activity = new Activity();
		$activity->lang_text = __('boilerplate::ui.updated', ["model" => class_basename($model) . " " . $model->{self::$nameColumn}]);
		$activity->affected()->associate($model);
		$activity->save();
	}

	protected static function deletingBy($model){
		$activity = new Activity();
        $activity->lang_text = __('boilerplate::ui.deleted', ["model" => class_basename($model) . " " . $model->{self::$nameColumn}]);
        $activity->save();
	}
}
