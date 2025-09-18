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
    public array $model_component = [];
	public array $data = [];

	public function loadModel(Request $request){
		if (property_exists($this, 'model')) {
            $modelClass = $this->model;
            $modelName = substr(strrchr($modelClass, '\\'), 1);
        } else {
            $modelName = ucfirst(Str::camel(str_replace('-', '_', $request->route('model'))));
            $modelClass = $modelName::class;
        }

        if (!class_exists($modelClass)) {
            throw new ErrorException($modelName .  " model not found!");
        }

        return str_replace( '_','-', Str::camel($modelName));
	}

    public function index(Request $request)
    {
		$model = Str::kebab($this->loadModel($request));

        if (empty($this->model_component['livewireComponents'])) {
            $this->model_component['livewireComponents'] = ($this->prefix ?? "") . $model . '.form';
        }

        if (empty($this->model_component['title'])) {
            $this->model_component['title'] = Lang::has('boilerplate::' . $model . '.create') ? __('boilerplate::' . $model . '.create') : __($model . '.create');
        }

        if (empty($this->model_component['static'])) {
            $this->model_component['static'] = true;
        }

        $json = json_encode($this->model_component, JSON_UNESCAPED_UNICODE);
        $jsObj = preg_replace('/"([a-zA-Z0-9_]+)":/', '$1:', $json);

        return view(($this->views['index'] ?? 'boilerplate::crud'), [
            'title'           => Lang::has('boilerplate::' . $model . '.plural') ? 'boilerplate::' . $model . '.plural' : $model . '.plural',
            'modal_component' => $jsObj,
            'page_component'  => ($this->prefix ?? "") . $model . '.data-table',
        ]);
    }
}
