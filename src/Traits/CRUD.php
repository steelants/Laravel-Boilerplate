<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

trait CRUD
{
	//public array $views = ['index' => 'crud.index']; change default blade
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
		$model = $this->loadModel($request);

        return view(($this->views['index'] ?? 'boilerplate::crud'), [
            'title'           => Lang::has('boilerplate::ui.' . $model . 's') ? 'boilerplate::ui.' . $model . 's' : 'ui.' . $model . 's',
            'modal_component' => $model . '.form',
            'page_component'  => $model . '.data-table',
        ]);
    }
}
