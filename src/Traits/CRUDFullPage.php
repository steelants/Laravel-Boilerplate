<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

trait CRUDFullPage
{
    use CRUD;
    //public array $views = ['index' => 'crud.index', 'form' => 'crud.form']; change default blade
    //public string $prefix = "";

	public function index(Request $request)
    {
		$model = Str::kebab($this->loadModel($request));

        return view(($this->views['index'] ?? 'boilerplate::crud'), [
            'title'           => Lang::has('boilerplate::' . $model . '.plural') ? 'boilerplate::' . $model . '.plural' : $model . '.plural',
            'full_page_component' => ($this->prefix ?? "") . $model . '.form',
            'page_component'  => ($this->prefix ?? "") . '.data-table',
        ]);
    }

	public function form(Request $request, $modelId = null)
    {
		$model = Str::kebab($this->loadModel($request));

		$data = [];
		if (!empty($modelId)) {
			$data['model'] = $modelId;
		}

        return view(($this->views['form'] ?? 'boilerplate::crud'), [
            'title'           => (Lang::has('boilerplate::' . $model . '.create') || Lang::has('boilerplate::' . $model . '.edit') ? 'boilerplate::' . $model : $model . '.') . (empty($modelId) ? 'create' : 'edit'),
            'page_component'  => ($this->prefix ?? "") . $model . '.form',
			'model_back' => ($this->prefix ?? "") . $model . '.index',
			'data' => $data,
        ]);
    }
}
