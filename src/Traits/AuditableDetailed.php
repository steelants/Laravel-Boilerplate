<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;

trait AuditableDetailed
{
	use Auditable;

	public static function updatingBy($model)
    {
		if (!$model->isDirty()) {
			return;
		}

		$collection = collect($model->getDirty());
		if (method_exists($model, 'auditableColumns')) {
			$collection = $collection->intersectByKeys(array_flip($model->auditableColumns()));
		}

		if (method_exists($model, 'auditableIgnored')) {
			$collection = $collection->except($model->auditableIgnored());
		}

		if (empty($collection) || count($collection) <= 0) {
			return;
		}

		$data = [];
		foreach ($collection as $key => $value) {
			$data[$key] = [
				"from" => $model->getOriginal($key),
				"to"   => $value,
			];
		}
		$activity = new Activity();
		$activity->lang_text = __('Updated :model', ['model' => __(class_basename($model) . " " . $model->{self::$nameColumn})]);
		$activity->data = $data;
		$activity->affected()->associate($model);
		$activity->save();
	}
}
