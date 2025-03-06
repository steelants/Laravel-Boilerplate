<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

trait FullPageCRUD
{
    //public array $viewName = ['crud.index', 'crud.form']; change default blade

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

        return view(($this->viewName[0] ?? 'boilerplate::crud'), [
            'title'           => Lang::has('boilerplate::ui.' . $model . 's') ? 'boilerplate::ui.' . $model . 's' : 'ui.' . $model . 's',
            'full_page_component' => $model . '.form',
            'page_component'  => $model . '.data-table',
        ]);
    }

	public function form(Request $request, $modelId = null)
    {
        $model = $this->loadModel($request);

		$data = [];
		if (!empty($modelId)) {
			$data['model'] = $modelId;
		}

        return view(($this->viewName[1] ?? 'boilerplate::crud'), [
            'title'           => (Lang::has('boilerplate::' . $model . '.create') || Lang::has('boilerplate::' . $model . '.edit') ? 'boilerplate::' . $model : $model . '.') . (empty($modelId) ? 'create' : 'edit'),
            'page_component'  => $model . '.form',
			'model_back' => $model,
			'data' => $data,
        ]);
    }
}
