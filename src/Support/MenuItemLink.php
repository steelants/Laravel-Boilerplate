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

	public function debug(){
		return [
			'current_url' =>route($this->resolveActiveRoute()->getName(), absolute:false),
			'url' =>route($this->route , absolute:false),
			'url_match' => ($this->resolveActiveRoute()->getName() === $this->route || str_starts_with($this->resolveActiveRoute()->getName(), $this->route.'.')),
			'current_route' =>$this->resolveActiveRoute()->getName(),
			'route' =>$this->route ,
			'route_match' => ($this->resolveActiveRoute()->getName() == $this->route || str_starts_with($this->resolveActiveRoute()->getName(),$this->route)),

		];
	}

	public function isUse(): bool
	{
		$current = $this->resolveActiveRoute();

		if (! $current || ! $currentName = $current->getName()) {
			return false;
		}

		$route = ($currentName === $this->route || str_starts_with($currentName, $this->route.'.'));
		$url = (route($currentName, absolute:false) == route($this->route , absolute:false) || str_starts_with(route($currentName, absolute:false), route($this->route , absolute:false)));

		return ($route && str_ends_with($route, '.index')) || ($url && route($this->route, absolute:false) != '/');
	}

	public function isActive(): bool
	{
		$current = $this->resolveActiveRoute();
		$query = $this->resolveQueryParameters();

		$route = ($current->getName() === $this->route );
		$url = (route($current->getName(), absolute:false) == route($this->route , absolute:false));

		if (count($this->parameters) == 0 && count($query) == 0){
			return ($route || $url);
		}

		if ($route || $url){
			return ($query == $this->parameters);
		}

		return False;
	}

	protected function resolveActiveRoute(): ?\Illuminate\Routing\Route
	{
		return once(function () {
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
}
