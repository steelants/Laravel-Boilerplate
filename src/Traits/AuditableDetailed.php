<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\Activity;

trait AuditableDetailed
{
    use Auditable;

    public static function updatingBy($model)
    {
        $collection = self::filterAuditableColumns($model);

        if ($collection->isEmpty()) {
            return;
        }

        $data = [];
        foreach ($collection as $key => $value) {
            $data[$key] = [
                'from' => $model->getOriginal($key),
                'to'   => $value,
            ];
        }

        $activity = new Activity;
        $activity->lang_text = __('Updated ' . class_basename($model) . ' ' . $model->{self::$nameColumn});
        $activity->data = $data;
        $activity->affected()->associate($model);
        $activity->save();
    }
}
