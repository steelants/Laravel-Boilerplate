<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Sada akcí nad Eloquent modelem - objekt se dohledá přes query() (včetně global scope tenanta). */
abstract class ModelActionSet extends ActionSet
{
    /** @var class-string<Model> */
    protected string $model;

    /** Rozsah, ve kterém se objekty z klíče hledají; přepiš pro with() relací, které akce čtou. */
    protected function query(): Builder
    {
        return $this->model::query();
    }

    public function resolve(string $key): Model
    {
        return $this->query()->findOrFail($key);
    }

    public function resolveMany(array $keys): array
    {
        return $this->query()->findMany($keys)
            ->keyBy(fn (Model $model) => (string) $model->getKey())
            ->all();
    }

    public function key(object $subject): string
    {
        /** @var Model $subject */
        return (string) $subject->getKey();
    }
}
