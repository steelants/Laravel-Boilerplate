<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions\Concerns;

use Closure;

/** Společný popis akce (Action i BulkAction): text, ikona, potvrzení, ability a destruktivnost. */
trait DescribesAction
{
    private string|Closure $label = '';

    private string|Closure $icon = '';

    private string|Closure|null $confirm = null;

    private ?string $ability = null;

    private bool $destructive = false;

    public function label(string|Closure $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string|Closure $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function confirm(string|Closure|null $confirm): self
    {
        $this->confirm = $confirm;

        return $this;
    }

    /** Ability (policy nebo gate), kterou musí mít uživatel nad objektem - platí pro zobrazení i spuštění. */
    public function can(string $ability): self
    {
        $this->ability = $ability;

        return $this;
    }

    public function destructive(bool $destructive = true): self
    {
        $this->destructive = $destructive;

        return $this;
    }

    public function ability(): ?string
    {
        return $this->ability;
    }

    public function isDestructive(): bool
    {
        return $this->destructive;
    }

    /** Closure zavolá container s danými parametry (objekt) a službami z DI. */
    private static function evaluate(mixed $value, array $parameters = []): mixed
    {
        return $value instanceof Closure ? app()->call($value, $parameters) : $value;
    }
}
