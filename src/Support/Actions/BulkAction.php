<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Support\Actions\Concerns\DescribesAction;

/**
 * Hromadná akce nad výběrem objektů. Obsluha buď po jednom (each - každý objekt projde can()/applicable()
 * zvlášť, chyba jednoho nezastaví ostatní), nebo najednou (handle - dostane kolekci objektů, které prošly):
 *
 *     BulkAction::make('markPaid')->authorize('has-manager')->can('update')
 *         ->applicable(fn (Invoice $invoice) => !$invoice->is_paid, __('Already paid'))
 *         ->each(fn (Invoice $invoice, InvoiceService $service) => $service->setPaid($invoice, true));
 *
 *     ->handle(fn (Collection $subjects, InvoiceService $service) => $service->markPaid($subjects));
 */
final class BulkAction
{
    use DescribesAction;

    private ?string $gate = null;

    private ?Closure $applicable = null;

    private ?string $notApplicableReason = null;

    private ?Closure $each = null;

    private ?Closure $handler = null;

    private function __construct(public readonly string $name)
    {
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    /** Smazání výběru přes policy 'delete' - každý objekt zvlášť. */
    public static function delete(): self
    {
        return self::make('remove')
            ->label(__('Remove'))
            ->icon('fas fa-trash')
            ->can('delete')
            ->destructive()
            ->each(function (Gate $gate, Model $subject): void {
                $gate->authorize('delete', $subject);
                $subject->delete();
            });
    }

    /** Ability bez objektu (gate), na které závisí, zda se akce vůbec nabídne - např. 'has-manager'. */
    public function authorize(string $ability): self
    {
        $this->gate = $ability;

        return $this;
    }

    /** Stav objektu, pro který akce dává smysl; ostatní se přeskočí s důvodem (v souhrnu výsledku). */
    public function applicable(Closure $condition, string $reason): self
    {
        $this->applicable = $condition;
        $this->notApplicableReason = $reason;

        return $this;
    }

    /** Obsluha po jednom objektu; ActionException / AuthorizationException objekt přeskočí s důvodem. */
    public function each(Closure $handler): self
    {
        $this->each = $handler;

        return $this;
    }

    /**
     * Obsluha celé kolekce najednou (parametr typu Collection nebo $subjects).
     * Může vrátit BulkResult, jinak se všechny předané objekty počítají jako zpracované.
     */
    public function handle(Closure $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function gate(): ?string
    {
        return $this->gate;
    }

    public function eachHandler(): ?Closure
    {
        return $this->each;
    }

    public function handler(): ?Closure
    {
        return $this->handler;
    }

    /** @return string|null důvod přeskočení, null = akce na objekt platí */
    public function notApplicableReason(object $subject): ?string
    {
        if ($this->applicable === null || self::evaluate($this->applicable, Action::parameters($subject))) {
            return null;
        }

        return $this->notApplicableReason;
    }

    public function labelText(): string
    {
        return (string) self::evaluate($this->label);
    }

    public function iconText(): string
    {
        return (string) self::evaluate($this->icon);
    }

    public function confirmText(): ?string
    {
        return self::evaluate($this->confirm);
    }
}
