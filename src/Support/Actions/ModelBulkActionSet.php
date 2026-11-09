<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use Illuminate\Database\Eloquent\Builder;

/** Hromadné akce nad Eloquent modelem - výběr se čte po dávkách (lazyById), ne celý do paměti. */
abstract class ModelBulkActionSet extends BulkActionSet
{
    /** @var class-string<\Illuminate\Database\Eloquent\Model> */
    protected string $model;

    /** Velikost dávky při čtení výběru. */
    protected int $chunkSize = 100;

    /** Rozsah, ve kterém se objekty z klíčů hledají; přepiš pro with() relací, které akce čtou. */
    protected function query(): Builder
    {
        return $this->model::query();
    }

    public function subjects(array $keys): iterable
    {
        if ($keys === []) {
            return [];
        }

        return $this->query()->whereKey($keys)->lazyById($this->chunkSize);
    }
}
