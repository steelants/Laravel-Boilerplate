<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Closure;
use Illuminate\Support\Facades\Gate;

/**
 * Fluent definice položky menu - obdoba Action::make(). Sama nic nevykresluje, builder (MenuBuilder::define(),
 * MenuItem::define()) z ní přes createMenuItem() postaví runtime položku (MenuItem, MenuItemLink, MenuItemAction):
 *
 *     MenuItem::make('tasks')->label(__('Tasks'))->icon('fas fa-list')->route('task.index')
 *         ->can('tasks')
 *         ->items([
 *             MenuItem::make('task.mine')->label(__('My tasks'))->route('task.index', ['assignees_ids' => $userId]),
 *         ])
 *         ->dropdown([
 *             MenuItem::make('task.create')->label(__('Create'))->action('wire:click="create"')->confirm(__('Sure?')),
 *         ]);
 *
 * Položku bez route i action (skupina) vykreslí jako MenuItem, typicky s ->as('toggle').
 */
final class PendingMenuItem
{
    private string|Closure $label = '';

    private string|Closure $icon = '';

    private ?string $route = null;

    private ?string $action = null;

    private array $parameters = [];

    private array $options = [];

    private bool|Closure $visible = true;

    /** @var array<int, array{0: string, 1: mixed}> */
    private array $abilities = [];

    private array|MenuSet|Closure $items = [];

    private array|MenuSet|Closure $dropdown = [];

    public function __construct(public readonly string $id) {}

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

    /** Položka je odkaz na pojmenovanou route (MenuItemLink - včetně zvýraznění isActive()/isUse()). */
    public function route(string $route, array $parameters = []): self
    {
        $this->route = $route;
        $this->action = null;

        return $parameters === [] ? $this : $this->parameters($parameters);
    }

    /** Položka spouští akci - HTML atributy tlačítka, např. `wire:click="delete(1)"` (MenuItemAction). */
    public function action(string $action): self
    {
        $this->action = $action;
        $this->route = null;

        return $this;
    }

    public function parameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function option(string $key, mixed $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function options(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /** Způsob vykreslení (option 'as'), např. 'toggle' pro rozbalovací skupinu. */
    public function as(string $as): self
    {
        return $this->option('as', $as);
    }

    public function confirm(?string $confirm): self
    {
        return $this->option('confirm', $confirm);
    }

    /** Podmínka zobrazení; closure zavolá container (DI). Vyhodnocuje se při sestavení menu. */
    public function visible(bool|Closure $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Ability (gate nebo policy), kterou musí mít přihlášený uživatel; lze volat opakovaně (musí platit všechny).
     * Ověřuje se přes $user->can(), takže platí i přepsané User::can() aplikace (např. vypnuté moduly).
     */
    public function can(string $ability, mixed $arguments = []): self
    {
        $this->abilities[] = [$ability, $arguments];

        return $this;
    }

    /** Podpoložky (subnavigace) - pole definic, MenuSet, nebo closure vracející jedno z nich. */
    public function items(array|MenuSet|Closure $items): self
    {
        $this->items = $items;

        return $this;
    }

    /** Položky dropdownu u položky - stejné formy jako items(). */
    public function dropdown(array|MenuSet|Closure $dropdown): self
    {
        $this->dropdown = $dropdown;

        return $this;
    }

    public function isVisible(): bool
    {
        $user = auth()->user();

        foreach ($this->abilities as [$ability, $arguments]) {
            if (!($user ? $user->can($ability, $arguments) : Gate::allows($ability, $arguments))) {
                return false;
            }
        }

        return (bool) self::evaluate($this->visible);
    }

    /**
     * Postaví runtime položku v rodiči (builder nebo položka) a rekurzivně její podpoložky a dropdown.
     * Neviditelnou položku přeskočí (včetně jejích potomků) a vrátí null.
     */
    public function attachTo(MenuBuilder|MenuItem $parent, bool $asDropdown = false): ?MenuItem
    {
        if (!$this->isVisible()) {
            return null;
        }

        $options = array_filter([
            'id'         => $this->id,
            'route'      => $this->route,
            'action'     => $this->action,
            'icon'       => (string) self::evaluate($this->icon),
            'parameters' => $this->parameters,
            'options'    => $this->options,
        ], fn ($value) => $value !== null);

        $title = (string) self::evaluate($this->label);

        $item = $asDropdown && $parent instanceof MenuItem
            ? $parent->addDropdown($title, $options)
            : $parent->add($title, $options);

        $item->define($this->items);
        $item->defineDropdown($this->dropdown);

        return $item;
    }

    /**
     * Normalizuje vstup items()/dropdown()/define() na seznam definic; null položky (podmíněné
     * `$cond ? MenuItem::make(...) : null`) vynechá.
     *
     * @return list<PendingMenuItem>
     */
    public static function resolve(array|MenuSet|Closure $items): array
    {
        $items = self::evaluate($items);

        if ($items instanceof MenuSet) {
            $items = $items->items();
        }

        return array_values(array_filter($items ?? [], fn ($item) => $item !== null));
    }

    private static function evaluate(mixed $value): mixed
    {
        return $value instanceof Closure ? app()->call($value) : $value;
    }
}
