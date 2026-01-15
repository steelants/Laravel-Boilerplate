<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class MenuItemLink extends MenuItem
{
	protected string $type = 'route';

    public function __construct(public string $title, public string $id, public string $route, public string $icon = '', public array $parameters = [], public array $options = [])
    {
        if (!Route::has($route)) {
            throw new Exception(__("Route with name: $route dont exists!"), 1);
        }
    }

	public function debug(): void
	{
		$path = trim(request()->path(), '/');
		$query = request()->getQueryString();
		$fullUrl = $path . ($query ? '?' . $query : '');

		if (str_contains($fullUrl, 'livewire')) {
			$fullUrl = request()->headers->get('referer');
		}

		$routeParts = explode('.', $this->route);
		$routeSegment = $routeParts[0];
		$routeAction  = $routeParts[1] ?? 'index';
		$routePath = trim(route($this->route, $this->parameters, false), '/');

		dump([
			'menu_route' => $this->route,
			'route_segment' => $routeSegment,
			'route_action' => $routeAction,
			'current_path' => $path,
			'query_string' => $query,
			'full_url' => $fullUrl,
			'route_path' => $routePath,
		]);

		$regex = '#/(?:[^/]+/)*' . preg_quote($routeSegment, '#') . '(?:/|$)#';

		dump([
			'segment_regex' => $regex,
			'regex_match' => preg_match($regex, '/' . $path),
			'is_index' => $routeAction === 'index',
		]);
	}

	public function isUse(): bool
	{
		$current = Route::currentRouteName();

		if (!$current) {
			return false;
		}

		return ($current === $this->route || str_starts_with($current, $this->route . '.'));
	}

  	 public function isActive(): bool
	{
		if (Route::currentRouteName() !== $this->route) {
			return false;
		}

		foreach ($this->parameters as $key => $value) {
			if ((string) request()->route($key) !== (string) $value) {
				return false;
			}
		}

		if ( empty($this->options['query']) && request()->query->count() > 0 ) {
			return false;
		}

		// 4️⃣ pokud menu query má, musí sedět
		if (!empty($this->options['query'])) {
			foreach ($this->options['query'] as $key => $value) {
				if ((string) request()->query($key) !== (string) $value) {
					return false;
				}
			}
		}

		return true;
	}
}
