<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Support\Actions\Concerns\DescribesAction;

/**
 * Definice jedné akce nad objektem: odkaz (url) nebo operace (handle). Hodnoty mohou být closure,
 * které container zavolá s objektem (podle typu nebo názvu parametru $subject) a službami z DI:
 *
 *     Action::make('unlock')->visible(fn (Invoice $invoice) => $invoice->is_locked)
 *         ->handle(fn (Invoice $invoice, InvoiceService $service) => $service->unlock($invoice));
 *
 * Definice je bezstavová - jedna instance slouží všem řádkům DataTable i menu v detailu.
 */
final class Action
{
    use DescribesAction;

    private string|Closure|null $url = null;

    private ?Closure $visible = null;

    private ?Closure $handler = null;

    private function __construct(public readonly string $name)
    {
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    /** Smazání přes policy 'delete', detail po něm přejde na ActionSet::indexUrl(). */
    public static function delete(): self
    {
        return self::make('remove')
            ->label(__('Remove'))
            ->icon('fas fa-trash')
            ->can('delete')
            ->destructive()
            ->handle(function (Gate $gate, Model $subject): ActionResult {
                $gate->authorize('delete', $subject);
                $subject->delete();

                return ActionResult::removed();
            });
    }

    /** Akce je odkaz - nic nespouští, jen naviguje. */
    public function url(string|Closure $url): self
    {
        $this->url = $url;

        return $this;
    }

    /** Podmínka stavu objektu (např. jen zamčená faktura) - platí pro zobrazení i spuštění. */
    public function visible(Closure $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /** Operace; může vrátit ActionResult, jinak se po ní obnoví zobrazení. */
    public function handle(Closure $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function isLink(): bool
    {
        return $this->url !== null;
    }

    public function handler(): ?Closure
    {
        return $this->handler;
    }

    public function isVisibleFor(object $subject): bool
    {
        return $this->visible === null || (bool) self::evaluate($this->visible, self::parameters($subject));
    }

    public function labelFor(object $subject): string
    {
        return (string) self::evaluate($this->label, self::parameters($subject));
    }

    public function iconFor(object $subject): string
    {
        return (string) self::evaluate($this->icon, self::parameters($subject));
    }

    public function urlFor(object $subject): ?string
    {
        return self::evaluate($this->url, self::parameters($subject));
    }

    public function confirmFor(object $subject): ?string
    {
        return self::evaluate($this->confirm, self::parameters($subject));
    }

    /** Parametry pro container: objekt podle názvu $subject i podle jeho třídy. */
    public static function parameters(object $subject): array
    {
        return ['subject' => $subject, $subject::class => $subject];
    }
}
