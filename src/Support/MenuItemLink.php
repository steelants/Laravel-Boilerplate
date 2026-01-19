<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Route;

class MenuItemLink extends MenuItem
{
    protected string $type = 'route';

    public function __construct(public string $title, public string $id, public string $route, public string $icon = '', public array $parameters = [], public array $options = [])
    {
        if (! Route::has($route)) {
            throw new Exception(__("Route with name: $route dont exists!"), 1);
        }
    }

    public function isUse(): bool
    {
        $current = $this->resolveActiveRoute();

        if (! $current || ! $currentName = $current->getName()) {
            return false;
        }

        return $currentName === $this->route || str_starts_with($currentName, $this->route.'.');
    }

    public function isActive(): bool
    {
        $route = $this->resolveActiveRoute();

        if (! $route || $route->getName() !== $this->route) {
            return false;
        }

        foreach ($this->parameters as $key => $value) {
            if ((string) $route->parameter($key) !== (string) $value) {
                return false;
            }
        }

        $queryParams = $this->resolveQueryParameters();

        if (empty($this->options['query']) && count($queryParams) > 0) {
            return false;
        }

        // 4️⃣ pokud menu query má, musí sedět
        if (! empty($this->options['query'])) {
            foreach ($this->options['query'] as $key => $value) {
                if (! array_key_exists($key, $queryParams) || (string) $queryParams[$key] !== (string) $value) {
                    return false;
                }
            }
        }

        return true;
    }

	protected function resolveActiveRoute(): ?\Illuminate\Routing\Route
	{
		$current = Route::current();

        if (! $current) {
            return null;
        }

        if ($current->getName() !== 'livewire.message') {
            return $current;
        }

        $referer = request()->headers->get('referer');

        if (! $referer) {
            return $current;
        }

        try {
            $matchRequest = HttpRequest::create($referer, 'GET');

            return app('router')->getRoutes()->match($matchRequest);
        } catch (\Throwable) {
            return $current;
        }
    }

    protected function resolveQueryParameters(): array
    {
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
    }
}
