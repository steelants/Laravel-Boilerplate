<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Support\Actions\Concerns\DescribesAction;

/**
 * Definice jedné akce nad objektem: odkaz (url), operace (handle), nebo volání metody Livewire komponenty
 * (livewire - jen DataTable). Hodnoty mohou být closure,
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

    private ?string $method = null;

    private mixed $parameters = null;

    private bool $listed = true;

    private function __construct(public readonly string $name)
    {
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    /** Smazání modelu přes policy 'delete' (ověří se při zobrazení i spuštění). */
    public static function delete(): self
    {
        return self::remove(function (Gate $gate, Model $subject): void {
            $gate->authorize('delete', $subject);
            $subject->delete();
        })->can('delete');
    }

    /**
     * Odstranění obsloužené vlastní službou, která si oprávnění ověřuje sama (např. kontroly navázaných dokladů).
     * Po akci vrací ActionResult::removed() - detail přejde na ActionSet::indexUrl(), DataTable obnoví řádky.
     */
    public static function remove(Closure $handler): self
    {
        return self::make('remove')
            ->label(__('Remove'))
            ->icon('fas fa-trash')
            ->destructive()
            ->handle(function (Container $container, object $subject) use ($handler): ActionResult {
                $container->call($handler, self::parameters($subject));

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

    /**
     * Akce obsloužená metodou Livewire komponenty (otevření modalu, stažení, ...) - nabízí se jen v DataTable,
     * která metodu má; menu v detailu ji vynechá. Parametry jsou výchozí klíč objektu, nebo hodnota/closure.
     */
    public function livewire(string $method, mixed $parameters = null): self
    {
        $this->method = $method;
        $this->parameters = $parameters;

        return $this;
    }

    /** Akci jde spustit (např. přepínač ve sloupci, RenderCasts\ToggleAction), ale v menu se nenabízí. */
    public function unlisted(): self
    {
        $this->listed = false;

        return $this;
    }

    public function isLink(): bool
    {
        return $this->url !== null;
    }

    public function isLivewire(): bool
    {
        return $this->method !== null;
    }

    public function isListed(): bool
    {
        return $this->listed;
    }

    public function method(): ?string
    {
        return $this->method;
    }

    /** Parametry volání Livewire metody; null = klíč objektu (doplní ActionRunner). */
    public function parametersFor(object $subject): mixed
    {
        return self::evaluate($this->parameters, self::parameters($subject));
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
