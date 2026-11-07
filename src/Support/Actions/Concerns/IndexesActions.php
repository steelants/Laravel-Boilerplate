<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions\Concerns;

use LogicException;

/** Definice akcí sady indexované podle názvu (ActionSet i BulkActionSet), sestavené jednou na instanci. */
trait IndexesActions
{
    /** @var array<string, object>|null */
    private ?array $indexedActions = null;

    abstract protected function define(): array;

    /** Kontrola definice (např. akce bez obsluhy) - vyhazuje LogicException. */
    abstract protected function validateAction(object $action): void;

    public function actions(): array
    {
        if ($this->indexedActions !== null) {
            return $this->indexedActions;
        }

        $this->indexedActions = [];
        foreach ($this->define() as $action) {
            if (isset($this->indexedActions[$action->name])) {
                throw new LogicException(static::class . " defines the action '{$action->name}' twice.");
            }

            $this->validateAction($action);
            $this->indexedActions[$action->name] = $action;
        }

        return $this->indexedActions;
    }

    public function find(string $name): ?object
    {
        return $this->actions()[$name] ?? null;
    }
}
