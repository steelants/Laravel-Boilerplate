<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

trait AuditableDetailed
{
	use Auditable{
        updatingBy as originalUpdatingBy;
    }

	public static function updatingBy($model){
		if ($model->isDirty()) {
			$data = [];
			foreach ($model->getDirty() as $key => $value) {
				$data[$key] = [
					"from" => $model->getOriginal($key),
					"to"   => $value,
				];
			}
			$activity = new Activity();
			$activity->lang_text = __('boilerplate::ui.updated', ["model" => class_basename($model) . " " . $model->{self::$nameColumn}]);
			$activity->data = $data;
			$activity->affected()->associate($model);
			$activity->save();
		} else {
			self::originalUpdatingBy($model);
		}
	}
}
