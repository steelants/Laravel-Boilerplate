<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use LogicException;
use SteelAnts\LaravelBoilerplate\Support\Actions\Concerns\IndexesActions;

/**
 * Hromadné akce jednoho typu objektu - samostatná sada vedle ActionSet. Vstupem je pole klíčů
 * (výběr v DataTable), sada z nich načte objekty. Pro Eloquent modely viz ModelBulkActionSet.
 *
 * @method array<string, BulkAction> actions()
 * @method BulkAction|null find(string $name)
 */
abstract class BulkActionSet
{
    use IndexesActions;

    /** @return list<BulkAction> */
    abstract protected function define(): array;

    /**
     * Objekty výběru; klíče mimo rozsah uživatele (jiný tenant, smazané) vynechá.
     *
     * @param  list<string>  $keys
     * @return iterable<object>
     */
    abstract public function subjects(array $keys): iterable;

    protected function validateAction(object $action): void
    {
        $handlers = $action instanceof BulkAction ? (int) ($action->eachHandler() !== null) + (int) ($action->handler() !== null) + (int) ($action->keysHandler() !== null) : 0;

        if ($handlers !== 1) {
            throw new LogicException(static::class . " bulk action '{$action->name}' must be a BulkAction with exactly one of each(), handle() or keys().");
        }

        if ($action->keysHandler() !== null && $action->hasSubjectChecks()) {
            throw new LogicException(static::class . " bulk action '{$action->name}' uses keys(), which cannot check can() or applicable() per object.");
        }
    }
}
