<?php

namespace SteelAnts\LaravelBoilerplate\Controllers\Http;

use SteelAnts\LaravelBoilerplate\Traits\CRUD;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;

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
                throw new ErrorException($modelName .  " model not found!");
            }
            $model = $modelName::class;
        }

        return view($this->viewName, [
            'title'           => 'boilerplate::ui.' . $model . 's',
            'modal_component' => $model . '.form',
            'page_component'  => $model . '.data-table',
        ]);
    }
}
