<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Livewire\Livewire;

trait CRUD
{
	//public array $views = ['index' => 'crud.index']; change default blade
    //public string $prefix = "";
    //public array $model_component = [];

	// public string $layout = "layout-app";
	public array $data = [];

	public function getRouteRoot(string $model, string $route): string
    {
        return (!empty($this->prefix) ? (Str::trim($this->prefix, '.') . '.') : '') . $model . '.' . Str::trim($route, '.');
    }

	public function loadModel(Request $request)
    {
		if (property_exists($this, 'model')) {
            $modelClass = $this->model;
            $modelName = substr(strrchr($modelClass, '\\'), 1);
        } else {
            $modelName = ucfirst(Str::camel(str_replace('-', '_', $request->route('model'))));
            $modelClass = $modelName::class;
        }

        if (!class_exists($modelClass)) {
            throw new ErrorException(__("$modelName model not found!"));
        }

        return str_replace('_', '-', Str::camel($modelName));
	}

    public function index(Request $request)
    {
		$model = Str::kebab($this->loadModel($request));

        $options = array_merge([
            'livewireComponents' => $this->getRouteRoot($model, 'form'),
            'title'              => __('Create ' . Str::of($this->loadModel($request))->headline()->lower()->toString()),
            'static'             => true,
        ], $this->model_component ?? []);

		//TODO: Fix if Better implementation is awailable is discoverable dont work if  extension of FormComponent is Extended form Component
		if (!Livewire::isDiscoverable($options['livewireComponents']) || (isset($this->model_component ) && $this->model_component != [])) {
			unset($options['livewireComponents']);
		}

        return view(($this->views['index'] ?? 'boilerplate::crud'), [
			'layout' 		 => $this->layout ?? config('boilerplate.layouts.default'),
            'title'          => __(Str::of($this->loadModel($request))->headline()->plural()->lower()->ucfirst()->toString()),
            'options'        => $options,
            'page_component' => $this->getRouteRoot($model, 'data-table'),
        ]);
    }
}
