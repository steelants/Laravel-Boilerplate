<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

trait CRUD
{
	//public array $views = ['index' => 'crud.index']; change default blade
    //public string $prefix = "";
    //public array $model_component = [];
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
            throw new ErrorException($modelName . " model not found!");
        }

        return str_replace('_', '-', Str::camel($modelName));
	}

    public function index(Request $request)
    {
		$model = Str::kebab($this->loadModel($request));
        $options = array_merge([
            'livewireComponents' => $this->getRouteRoot($model, 'form'),
            'title'              => Lang::has('boilerplate::' . $model . '.create') ? __('boilerplate::' . $model . '.create') : __($model . '.create'),
            'static'             => true,
        ], $this->model_component ?? []);

        return view(($this->views['index'] ?? 'boilerplate::crud'), [
            'title'          => Lang::has('boilerplate::' . $model . '.plural') ? 'boilerplate::' . $model . '.plural' : $model . '.plural',
            'options'        => $options,
            'page_component' => $this->getRouteRoot($model, 'data-table'),
        ]);
    }
}
