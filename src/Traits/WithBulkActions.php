<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use LogicException;
use SteelAnts\DataTable\Traits\HasBulkActions;
use SteelAnts\LaravelBoilerplate\Attributes\UseBulkActions;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionItem;
use SteelAnts\LaravelBoilerplate\Support\Actions\ActionRunner;
use SteelAnts\LaravelBoilerplate\Support\Actions\BulkActionSet;
use SteelAnts\LaravelBoilerplate\Traits\HandlesActionErrors;

/**
 * Hromadné akce DataTable ze sady #[UseBulkActions(InvoiceBulkActions::class)] nad komponentou.
 * Výběr a lištu řeší balíčkový HasBulkActions; zaškrtávat jde jen tehdy, když se uživateli nabízí
 * aspoň jedna akce. Pro each()/handle() se výběr omezí na query() tabulky (selectedQuery), podvržené klíče neprojdou;
 * keys() akce dostane výběr tak, jak je, a rozsah hlídá její služba.
 */
trait WithBulkActions
{
    use HandlesActionErrors;
    use HasBulkActions;

    private ?BulkActionSet $bulkActionSet = null;

    /** @var list<ActionItem>|null */
    private ?array $bulkItems = null;

    /** Definice hromadných akcí pro lištu (HasBulkActions::bulkActions()). */
    public function bulkActions(): array
    {
        return array_map(fn (ActionItem $item) => array_filter([
            'type'        => 'livewire',
            'action'      => $item->name,
            'text'        => $item->text,
            'iconClass'   => $item->icon,
            'actionClass' => $item->destructive ? 'btn-outline-danger' : null,
            'confirm'     => $item->confirm,
        ], fn ($value) => $value !== null), $this->bulkItems());
    }

    public function canSelect(): bool
    {
        return $this->bulkItems() !== [];
    }

    /** Spustí akci nad výběrem přes ActionRunner a výsledek (zpracováno / přeskočeno s důvodem) ukáže jako alert. */
    public function callBulkAction(int|string $action): mixed
    {
        if (!$this->selectionEnabled() || !is_string($action)) {
            return null;
        }

        $set = $this->bulkActionSet();

        // keys() akce předá výběr rovnou službě (bez dotazu), ostatní klíče ověří proti query() tabulky.
        if ($set->find($action)?->keysHandler() !== null) {
            $keys = $this->selected;
        } else {
            $query = $this->selectedQuery();
            $keys = array_map('strval', $query->pluck($query->getModel()->getQualifiedKeyName())->all());
        }

        $this->attempt(function () use ($set, $keys, $action) {
            $result = app(ActionRunner::class)->runBulk($set, $keys, $action);
            $message = $this->bulkResultMessage($result);

            $result->hasProblems() ? alert()->warning($message)->now() : alert()->success($message)->now();
            $this->clearSelection();
        });

        return null;
    }

    /** @return list<ActionItem> */
    private function bulkItems(): array
    {
        return $this->bulkItems ??= app(ActionRunner::class)->bulkItems($this->bulkActionSet());
    }

    protected function bulkActionSet(): BulkActionSet
    {
        return $this->bulkActionSet ??= app(UseBulkActions::on($this) ?? throw new LogicException(static::class . ' needs the #[UseBulkActions(...)] attribute.'));
    }
}
