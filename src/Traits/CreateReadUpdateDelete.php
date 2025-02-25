<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Support\Str;
use ErrorException;
use Illuminate\Http\Request;

trait CreateReadUpdateDelete
{
    public string $viewName = 'boilerplate::crud';

    public function index(Request $request)
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
