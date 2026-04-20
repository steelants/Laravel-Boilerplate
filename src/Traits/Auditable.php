<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use SteelAnts\LaravelBoilerplate\Models\Activity;

trait Auditable
{
    protected static $nameColumn = 'name';

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
            if (self::filterAuditableColumns($model)->isNotEmpty()) {
                self::updatingBy($model);
            }
        });

        static::deleting(function ($model) {
            self::deletingBy($model);
        });
    }

    protected static function filterAuditableColumns($model): Collection
    {
        $dirty = collect($model->getDirty());
        $explicit = method_exists($model, 'auditableColumns') ? $model->auditableColumns() : [];
        $ignored = array_diff(
            array_merge(
                method_exists($model, 'auditableIgnored') ? $model->auditableIgnored() : [],
                $model->getHidden(),
                ($explicit ? $dirty->keys()->diff($explicit)->all() : [])
            ),
            $explicit
        );

        return $dirty->except($ignored);
    }

    protected static function createdBy($model)
    {
        $activity = new Activity;
        $activity->lang_text = __('Created ' . class_basename($model) . ' ' . $model->{self::$nameColumn});
        $activity->affected()->associate($model);
        $activity->save();
    }

    protected static function updatingBy($model)
    {
        $activity = new Activity;
        $activity->lang_text = __('Updated ' . class_basename($model) . ' ' . $model->{self::$nameColumn});
        $activity->affected()->associate($model);
        $activity->save();
    }

    protected static function deletingBy($model)
    {
        $activity = new Activity;
        $activity->lang_text = __('Removed ' . class_basename($model) . ' ' . $model->{self::$nameColumn});
        $activity->save();
    }
}
