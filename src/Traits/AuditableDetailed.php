<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

trait AuditableDetailed
{
	use Auditable;

	public static function updatingBy($model){
		if (!$model->isDirty()) {
			return;
		}

		$collection = collect($model->getDirty());
		if (method_exists($model, 'auditedColumns')){
			$collection = $collection->intersectByKeys(array_flip($model->auditedColumns()));
		}

		if (method_exists($model, 'auditedExceptions')){
			$collection = $collection->except($model->auditedExceptions());
		}

		$data = [];
		foreach ($collection as $key => $value) {
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
	}
}
