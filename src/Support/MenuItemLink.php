<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Route;

/**
 * Menu item pointing at a named route.
 *
 * Highlighting answers two questions:
 *
 *  - isActive() - does this item point exactly at what is on screen right now?
 *    The route name has to match exactly and the item parameters have to match.
 *
 *  - isUse()    - does the current page live "below" this item? The route name may
 *    be a descendant (`tasks.index` -> `tasks.edit`), parameters still have to match.
 *
 * Parameters are only compared on the keys that distinguish items on the given menu
 * level, meaning the keys declared by at least one item pointing at the same route on
 * that level (see scopedParameterKeys()). That way `?page=2` or `?search=foo` in the
 * URL does not break the highlight, while siblings `?filter=9` and `?filter=10` still
 * tell each other apart and an item without a filter ("All") is not active on
 * `?filter=9`.
 */
class MenuItemLink extends MenuItem
{
    protected string $type = 'route';

    public function __construct(public string $title, public string $id, public string $route, public string $icon = '', public array $parameters = [], public array $options = [])
    {
        if (!Route::has($route)) {
            throw new Exception(__("Route with name: $route dont exists!"), 1);
        }
    }

    public function debug()
    {
        $current = $this->resolveActiveRoute();

        return [
            'route'              => $this->route,
            'current_route'      => $current?->getName(),
            'route_match'        => $this->matchRoute(),
            'url'                => route($this->route, $this->getItemParameters(), absolute: false),
            'current_url'        => request()->getRequestUri(),
            'item_parameters'    => $this->getItemParameters(),
            'current_parameters' => $this->currentParameters(),
            'scoped_keys'        => $this->scopedParameterKeys(),
            'parameters_match'   => $this->matchParameters(),
            'is_use'             => $this->isUse(),
            'is_active'          => $this->isActive(),
        ];
    }

    public function isUse(): bool
    {
        return $this->matchRoute() && $this->matchParameters();
    }

    public function isActive(): bool
    {
        $current = $this->resolveActiveRoute();

        if (!$current || $current->getName() !== $this->route) {
            return false;
        }

        return $this->matchParameters();
    }

    /**
     * Does the item point at the current route or at one of its ancestors?
     * `system.user.index` is also in use on `system.user.edit` or `system.user.show`.
     */
    protected function matchRoute(): bool
    {
        $currentName = $this->resolveActiveRoute()?->getName();

        if (!$currentName) {
            return false;
        }

        return $currentName === $this->route || str_starts_with($currentName, $this->routeGroup() . '.');
    }

    /**
     * Do the item parameters match the current request on the distinguishing keys?
     */
    protected function matchParameters(): bool
    {
        $itemParameters = $this->getItemParameters();
        $currentParameters = $this->currentParameters();

        foreach ($this->scopedParameterKeys() as $key) {
            if (!$this->sameValue($itemParameters[$key] ?? null, $currentParameters[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Keys compared on this menu level: the item's own parameters plus the parameters
     * of siblings pointing at the same route. Anything else in the URL (paging,
     * sorting, fulltext) is ignored.
     *
     * @return array<int, string>
     */
    protected function scopedParameterKeys(): array
    {
        $keys = array_keys($this->getItemParameters());

        foreach ($this->siblings() ?? [] as $sibling) {
            if ($sibling === $this || !$sibling instanceof self || $sibling->route !== $this->route) {
                continue;
            }

            $keys = array_merge($keys, array_keys($sibling->getItemParameters()));
        }

        return array_values(array_unique($keys));
    }

    /**
     * Route prefix used to look up descendants: `tasks.index` -> `tasks`.
     */
    protected function routeGroup(): string
    {
        if (!str_ends_with($this->route, '.index')) {
            return $this->route;
        }

        return substr($this->route, 0, -strlen('.index'));
    }

    /**
     * Parameters of the current request - route parameters and the query string
     * together, the same way the item mixes them in its own `parameters`.
     */
    protected function currentParameters(): array
    {
        return $this->resolveQueryParameters() + $this->resolveRouteParameters();
    }

    /**
     * Values coming from a URL are always strings, menu definitions usually use ints.
     */
    protected function sameValue(mixed $itemValue, mixed $currentValue): bool
    {
        if ($itemValue === null || $currentValue === null) {
            return $itemValue === null && $currentValue === null;
        }

        if (is_scalar($itemValue) && is_scalar($currentValue)) {
            return (string) $itemValue === (string) $currentValue;
        }

        return $itemValue == $currentValue;
    }

    protected function resolveActiveRoute(): ?\Illuminate\Routing\Route
    {
        return once(function () {
            $current = Route::current();

            if (!$current) {
                return null;
            }

            if ($current->getName() !== 'livewire.message') {
                return $current;
            }

            $referer = request()->headers->get('referer');

            if (!$referer) {
                return $current;
            }

            try {
                $matchRequest = HttpRequest::create($referer, 'GET');

                return app('router')->getRoutes()->match($matchRequest);
            } catch (\Throwable) {
                return $current;
            }
        });
    }

    protected function resolveQueryParameters(): array
    {
        return once(function () {
            if (Route::currentRouteName() === 'livewire.message') {
                $referer = request()->headers->get('referer');
                if ($referer) {
                    $queryString = parse_url($referer, PHP_URL_QUERY);

                    if ($queryString) {
                        parse_str($queryString, $query);

                        return $query;
                    }

                    return [];
                }
            }

            return request()->query->all();
        });
    }

    protected function resolveRouteParameters(): array
    {
        return once(function () {
            $current = $this->resolveActiveRoute();

            if (!$current) {
                return [];
            }

            return collect($current->originalParameters())->filter(function ($value, $key) {
                return $value != null;
            })->toArray();
        });
    }

    protected function getItemParameters(): array
    {
        return collect($this->parameters)->filter(function ($value, $key) {
            return $value != null;
        })->toArray();
    }
}
