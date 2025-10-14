<?php

namespace SteelAnts\LaravelBoilerplate\Controllers\Http;

use App\Http\Controllers\Controller;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Traits\CRUD;

class CrudController extends Controller
{
    use CRUD;

    public string $viewName = 'boilerplate::crud';

    public function model(Request $request)
    {
        if (property_exists($this, 'model')) {
            $model = $this->model;
        } else {
            $modelName = ucfirst(Str::camel(str_replace('-', '_', $request->route('accountId'))));
            if (!class_exists('App\\Models\\' . $modelName)) {
                throw new ErrorException(__(":modelName model not found!", ['modelName' => $modelName]));
            }
            $model = $modelName::class;
        }

        return view($this->viewName, [
            'title'           => __(Str::of(class_basename($model))->headline()->plural()->lower()->ucfirst()->toString()),
            'modal_component' => $model . '.form',
            'page_component'  => $model . '.data-table',
        ]);
    }
}
