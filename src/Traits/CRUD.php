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
            $model = $this->model;
        } else {
            $modelName = ucfirst(Str::camel(str_replace('-', '_', $request->route('accountId'))));
            if (!class_exists('App\\Models\\' . $modelName)) {
                throw new ErrorException($modelName .  " model not found!");
            }
            $model = $modelName::class;
        }
		return $model;
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
