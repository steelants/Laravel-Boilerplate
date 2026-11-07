<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use LogicException;
use SteelAnts\LaravelBoilerplate\Attributes\UseActions;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionItem;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionResult;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionSet;
use SteelAnts\LaravelBoilerplate\Traits\HandlesActionErrors;

/**
 * Akce řádku DataTable ze sady #[UseActions(InvoiceActions::class)] nad komponentou.
 * Akce se vyhodnocují nad modelem, ne nad polem řádku: modely celé stránky se načtou jedním dotazem.
 * Odmítnutou akci (AuthorizationException / ActionException) zobrazí jako alert.
 */
trait WithActions
{
    use HandlesActionErrors;

    private ?ActionSet $actionSet = null;

    /** @var array<string, object> */
    private array $actionSubjects = [];

    /** Definice akcí řádku (DataTable::actions()). */
    public function actions($row): array
    {
        $key = (string) data_get($row, $this->keyPropery);

        // getData() vynuluje $this->actions před prvním řádkem - tehdy načteme objekty celé stránky naráz.
        if ($this->actions === [] || !array_key_exists($key, $this->actionSubjects)) {
            $keys = array_map('strval', array_column($this->dataset, $this->keyPropery));
            $this->actionSubjects = $this->actionSet()->resolveMany(array_values(array_unique([...$keys, $key])));
        }

        $subject = $this->actionSubjects[$key] ?? null;
        if ($subject === null) {
            return [];
        }

        return array_map(
            fn (ActionItem $item) => $this->toDataTableAction($item, $key),
            app(ActionRunner::class)->items($this->actionSet(), $subject),
        );
    }

    /** Volá se z definice akce: parameters = [název akce, klíč objektu]. */
    public function runAction(array $call): void
    {
        [$name, $key] = array_pad(array_values($call), 2, null);
        abort_unless(is_string($name) && is_scalar($key), 404);

        $set = $this->actionSet();
        $subject = $set->resolve((string) $key);

        $this->attempt(function () use ($set, $subject, $name) {
            $result = app(ActionRunner::class)->run($set, $subject, $name);

            if ($result->redirect !== null) {
                $this->redirect($result->redirect, navigate: true);
            }

            $this->afterAction($name, $result);
        });
    }

    /** Hook po úspěšné akci (alert, event pro okolní komponenty, ...). */
    protected function afterAction(string $name, ActionResult $result): void
    {
    }

    protected function actionSet(): ActionSet
    {
        return $this->actionSet ??= app(UseActions::on($this) ?? throw new LogicException(static::class . ' needs the #[UseActions(...)] attribute.'));
    }

    private function toDataTableAction(ActionItem $item, string $key): array
    {
        $action = match (true) {
            $item->isLink()     => ['type' => 'url', 'url' => $item->url],
            $item->isLivewire() => ['type' => 'livewire', 'action' => $item->method, 'parameters' => $item->parameters],
            default             => ['type' => 'livewire', 'action' => 'runAction', 'parameters' => [$item->name, $key]],
        };
        $action['confirm'] = $item->confirm;

        return array_filter($action + [
            'text'        => $item->text,
            'iconClass'   => $item->iconClass(),
            'actionClass' => $item->actionClass(),
        ], fn ($value) => $value !== null);
    }
}
