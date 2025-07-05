<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

trait CRUDFullPage
{
    use CRUD;
    //public array $views = ['index' => 'crud.index', 'edit' => 'crud.edit']; change default blade

	public function edit(Request $request, $modelId = null)
    {
        $model = $this->loadModel($request);

		$data = [];
		if (!empty($modelId)) {
			$data['model'] = $modelId;
		}

        return view(($this->views['edit'] ?? 'boilerplate::crud'), [
            'title'           => (Lang::has('boilerplate::' . $model . '.create') || Lang::has('boilerplate::' . $model . '.edit') ? 'boilerplate::' . $model : $model . '.') . (empty($modelId) ? 'create' : 'edit'),
            'page_component'  => $model . '.edit',
			'model_back' => $model . '.index',
			'data' => $data,
        ]);
    }
}
