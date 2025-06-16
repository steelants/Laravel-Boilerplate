<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait AuditableDetailed
{
	use Auditable{
        updatingBy as originalUpdatingBy;
    }

	public function updatingBy($model){
		if ($model->isDirty()) {
			foreach ($model->getAttributes() as $key => $value) {
				if (!$model->isDirty($key)) {
					continue;
				}

				$activity = new Activity();
				$activity->lang_text = __('boilerplate::ui.updated', ["model" => class_basename($model) . " " . $model->name]);
				$activity->affected()->associate($model);

				$activity->data = [
					$key => [
						"from" => $model->getOriginal($key),
						"to"   => $model->$key,
					],
				];

				$activity->save();
			}
		} else {
			$this->originalUpdatingBy($model);
		}
	}
}
