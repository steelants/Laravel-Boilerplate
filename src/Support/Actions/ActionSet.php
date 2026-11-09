<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use LogicException;
use SteelAnts\LaravelBoilerplate\Support\Actions\Concerns\IndexesActions;

/**
 * Všechny akce jednoho typu objektu (model, log, event, ...) - jediný zdroj pravdy pro řádek DataTable
 * i menu v detailu. Kromě definice akcí umí objekt načíst z klíče, který přišel z prohlížeče.
 * Pro Eloquent modely viz ModelActionSet, hromadné akce nad výběrem viz BulkActionSet.
 *
 * @method array<string, Action> actions()
 * @method Action|null find(string $name)
 */
abstract class ActionSet
{
    use IndexesActions;

    /** @return list<Action> */
    abstract protected function define(): array;

    /** Načte objekt podle klíče; neexistující (nebo mimo rozsah uživatele) končí výjimkou. */
    abstract public function resolve(string $key): object;

    abstract public function key(object $subject): string;

    /**
     * Objekty celé stránky DataTable najednou (ModelActionSet jedním dotazem). Chybějící klíče vynechá.
     *
     * @param  list<string>  $keys
     * @return array<string, object>
     */
    public function resolveMany(array $keys): array
    {
        $subjects = [];
        foreach ($keys as $key) {
            $subjects[$key] = $this->resolve($key);
        }

        return $subjects;
    }

    /** Kam přejít z detailu, když objekt akcí zanikne (ActionResult::removed()). */
    public function indexUrl(object $subject): ?string
    {
        return null;
    }

    protected function validateAction(object $action): void
    {
        $kinds = $action instanceof Action ? (int) $action->isLink() + (int) $action->isLivewire() + (int) ($action->handler() !== null) : 0;

        if ($kinds !== 1) {
            throw new LogicException(
                static::class . " action '{$action->name}' must be an Action with exactly one of url(), livewire() or handle()."
            );
        }
    }
}
