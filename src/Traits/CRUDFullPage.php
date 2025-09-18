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
    //public string $prefix = '';
    
    private function getRouteRoot(string $route, string $model): string{
        return (isset($this->prefix) ? ($this->prefix . '.') : null) . $model .'.'. Str::trim($route, '.');
    }
    
    public function index(Request $request)
    {
        $model = Str::kebab($this->loadModel($request));
        
        return view(($this->views['index'] ?? 'boilerplate::crud'), [
            'title'               => Lang::has('boilerplate::ui.' . $model . 's') ? 'boilerplate::ui.' . $model . 's' : 'ui.' . $model . 's',
            'full_page_component' => $this->getRouteRoot('form', $model),
            'page_component'      => $this->getRouteRoot('data-table', $model),
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
            'page_component'  => $this->getRouteRoot('form', $model),
            'model_back'      => $this->getRouteRoot('index', $model),
            'data'            => $data,
        ]);
    }
}
