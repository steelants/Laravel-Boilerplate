<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use SteelAnts\LaravelBoilerplate\Helpers\AbstractHelper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {model}
                            {--namespace=/ : Overwrite existing files by default}
                            {--force : Overwrite existing files by default}
							{--full-page-components : Make form full page}'; // {--view : Generate controller and blade files}

    protected $description = 'Creates CRUD for specified Command';

    protected function getPackageBasePath(): string
    {
        return __DIR__.'/../../..';
    }

    protected function getComponentRootnamespace(string $namespace): string
    {
        return Str::replace('/', '\\', $namespace);
    }

    protected function getRelativeNamespacePath(string $absolutNamespacePath): string
    {
        return Str::replace(base_path(), '', $absolutNamespacePath);
    }

    protected function ensurePath($path)
    {
        if (! file_exists($path)) {
            $fs = new Filesystem();
            $path = $path;
            $fs->makeDirectory($path, 0755, true, true);
        }
    }

    public function handle(): void
    {
        $model = ucfirst($this->argument('model'));
        $modelClass = (new ('App\\Models\\'.$model));
        if (! class_exists($modelClass::class)) {
            $this->components->error($modelClass.' model not Found!');
            return;
        }
        $namespacesAbsolut = [
            'controller' => ($this->getComponentRootnamespace('App\\Http\\Controllers'. ($this->option('namespace') != '/' ? '\\' . Str::trim($this->option('namespace'), '\\') : ""))),
            'livewire'   => ($this->getComponentRootnamespace('App\\Livewire'.($this->option('namespace') != '/' ? '\\' . Str::trim($this->option('namespace'), '\\') : '')).'\\'.$model),
        ];

        foreach ($namespacesAbsolut as $namespaceAbsolut) {
            $this->ensurePath(AbstractHelper::namespaceToPath($namespaceAbsolut));
        }

        $fillables = $modelClass->getFillable();
        if ($fillables == []) {
            $this->components->warn('Please make sure that $fillable variable of model '.$modelClass.' is defined correctly.');
        }

        $casts = $modelClass->getCasts();
        $properties = [];
        foreach ($fillables as $fillable) {
            $modelClassName = ucfirst(Str::camel(Str::replace('_id', '', $fillable, false)));
            $finalCast = 'string';
            if (Str::contains($fillable, '_id', true) && class_exists('App\\Models\\'.$modelClassName)) {
                if (method_exists($modelClass, Str::camel($modelClassName))) {
                    $finalCast = 'App\\Models\\'.$modelClassName;
                }
            } elseif (isset($casts[$fillable])) {
                $finalCast = $casts[$fillable];
            }

            $properties[$fillable] = $finalCast;
        }

        $safeProperties = array_diff_key($modelClass->getFillable(), $modelClass->getHidden());

        $this->makeClassFile($namespacesAbsolut['livewire'], AbstractHelper::namespaceToPath($namespacesAbsolut['livewire']), 'Form.php', $model, $properties, $safeProperties);
        $this->makeClassFile($namespacesAbsolut['livewire'], AbstractHelper::namespaceToPath($namespacesAbsolut['livewire']), 'DataTable.php', $model, $properties, $safeProperties);
        $this->makeClassFile($namespacesAbsolut['controller'], AbstractHelper::namespaceToPath($namespacesAbsolut['controller']), (Str::trim($model, "\\").'Controller.php'), $model, $properties, $safeProperties);
    }

    private function makeClassFile(string $namespace, string $path, string $fileName, string $model, array $properties, array $safeProperties)
    {
        $modifiedSceletonFilePath = ($path.DIRECTORY_SEPARATOR.$fileName);
        if (file_exists($modifiedSceletonFilePath) && ! $this->option('force')) {
            if (! $this->components->confirm('The ['.$modifiedSceletonFilePath.'] test already exists. Do you want to replace it?')) {
                return;
            }
        }

        $folderpath = str_replace(DIRECTORY_SEPARATOR.$fileName, '', $modifiedSceletonFilePath);
        $this->ensurePath($folderpath);
        $this->components->info('creating File: '.$modifiedSceletonFilePath);

        $livewireDotPath = Str::remove('App\\Livewire\\', ($namespace));
        if (!Str::contains($livewireDotPath, $model)) {
            $livewireDotPath .= '\\'.$model;
        }

        $livewireDotPath = Str::remove('App\\Http\\Controllers\\', $livewireDotPath);
        $livewireDotPath = Str::replace(DIRECTORY_SEPARATOR, '.', $livewireDotPath);
        $livewireDotPath = Str::trim($livewireDotPath, '.');

        foreach (explode('.', $livewireDotPath) as $pathPart) {
            $livewireDotPath = Str::replace($pathPart, Str::kebab($pathPart), $livewireDotPath);
        }

        $routeprefix = Str::trim(Str::remove(Str::lower(Str::kebab($model)), $livewireDotPath), '.');
        $route = $livewireDotPath . '.index';

        if ($fileName == 'Form.php') {
            Artisan::call('make:livewire '.$livewireDotPath.'.Form --force');

            $viewName = 'livewire.'.Str::replace('.index', '.form', $route);
            $formClassContent = $this->getFormClassSkeleton([
                'namespace'      => $namespace,
                'model'          => $model,
                'view'           => $viewName,
                'properties'     => $properties,
                'safeProperties' => $safeProperties,
                'action_back'    => $this->option('full-page-components') ? '$this->redirectRoute(\''.$route.'\');' : '',
                'isModal'        => $this->option('full-page-components') ? 'false' : 'true',
            ]);

            $bladePathFile = explode((DIRECTORY_SEPARATOR . 'app'), (str_replace((DIRECTORY_SEPARATOR.$fileName), '', $modifiedSceletonFilePath)))[0];
            $bladePathFile = ($bladePathFile.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.Str::replace('.', DIRECTORY_SEPARATOR, $viewName).'.blade.php');

            file_put_contents($modifiedSceletonFilePath, $formClassContent);

            $modalBladeContent = $this->getFormBladeSkeleton([
                'model'          => $model,
                'properties'     => $properties,
                'safeProperties' => $safeProperties,
            ]);

            file_put_contents($bladePathFile, $modalBladeContent);
        } elseif ($fileName == 'DataTable.php') {
            $datatableClassContent = $this->getDataTableClassSkeleton([
                'namespace'   => $namespace,
                'model'       => $model,
                'headers'     => $safeProperties,
                'action_form' => Str::replace('.index', '.form', $route),
            ]);

            file_put_contents($modifiedSceletonFilePath, $datatableClassContent);
        } elseif (Str::contains($fileName, 'Controller.php')) {
            $controllerClassContent = $this->getControllerSkeleton([
                'namespace'  => $namespace,
                'model'      => $model,
                'model_name' => Str::lower($model),
                'trait'      => $this->option('full-page-components') ? 'CRUDFullPage' : 'CRUD',
                'overides'   => 'public string $prefix = \''.$routeprefix.'\';',
            ]);

            file_put_contents($modifiedSceletonFilePath, $controllerClassContent);

            $routeFilePath = (base_path() . DIRECTORY_SEPARATOR.'routes' . DIRECTORY_SEPARATOR.'web.php');

            // index route
            $routesToadd = ['index' => $route];
            if ($this->option('full-page-components')) {
                $fornRoute = Str::replace('.index', '.form', $route);
                $routesToadd['form' ] = $fornRoute;
            }

            foreach ($routesToadd as $function => $route) {
                if (!Route::has($route)) {
                    $this->components->info('creating route: '. $route . ' inside: '. $routeFilePath);
                    $routeFileContent = "\nRoute::get('/".Str::replace('.', '/', Str::remove('.index', $route))."', [".$namespace. '\\' . Str::remove('.php', $fileName)."::class, '". $function ."'])->name('".$route."');";
                    file_put_contents($routeFilePath, $routeFileContent, FILE_APPEND);
                } else {
                    $this->components->warn('found route: '.$route. ' inside: '. $routeFilePath);
                }
            }
        }
    }

    private function getDataTableClassSkeleton(array $arguments)
    {
        $arguments['model_camel_case'] = Str::camel($arguments['model']);
        $arguments['model_snake_case'] = Str::snake($arguments['model_camel_case']);
        $arguments['model_singular'] = Str::of($arguments['model'])->headline()->singular()->lower()->toString();

        $headerProperties = '';
        foreach ($arguments['headers'] as $key => $header) {
            $headerProperties .= "\t\t\t'".$header."' => __('".Str::of($header)->headline()->lower()->ucfirst()->toString()."'),\n";
        }
        $arguments['headerProperties'] = rtrim(ltrim($headerProperties, "\t"), "\n");
        unset($arguments['headers']);

        $stubFilePath = $this->option('full-page-components') ? ('/stubs/FullPageDataTable.stub') : ('/stubs/DataTable.stub');
        $moduleRootPath = realpath($this->getPackageBasePath().$stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            if (gettype($ArgumentValue) != 'string') {
                continue;
            }

            $fileContent = str_replace('{{'.$ArgumentName.'}}', $ArgumentValue, $fileContent);
        }

        return $fileContent;
    }

    private function getFormClassSkeleton(array $arguments)
    {
        $arguments['model_camel_case'] = Str::camel($arguments['model']);
        $arguments['model_snake_case'] = Str::snake($arguments['model_camel_case'], '-');

        $arguments['uses'] = '';
        $arguments['modelFunctions'] = '';
        $propertiesString = '';
        $validationRules = '';
        $loadProperties = '';

        $safeToEditProperties = array_intersect_key($arguments['properties'], array_flip($arguments['safeProperties']));
        foreach ($safeToEditProperties as $propertyName => $propertyType) {
            $rule = 'required|'.$propertyType;
            $publicType = $propertyType;
            if (Str::contains($propertyType, 'App\\Models\\')) {
                $tableName = (new $propertyType())->getTable();
                $rule = 'required|exists:'.$tableName.',id';
                $publicType = '';

                $stubFilePath = ('/stubs/ModelForm.stub');
                $moduleRootPath = realpath($this->getPackageBasePath().$stubFilePath);
                $fileContent = file_get_contents($moduleRootPath, true);
                $modelArguments = [
                    'model_camel_case_plural' => Str::camel($tableName),
                    'model'                   => Str::afterLast($propertyType, 'Models\\'),
                ];
                foreach ($modelArguments as $ArgumentName => $ArgumentValue) {
                    if (gettype($ArgumentValue) != 'string') {
                        continue;
                    }

                    $fileContent = str_replace('{{'.$ArgumentName.'}}', $ArgumentValue, $fileContent);
                }
                $arguments['modelFunctions'] .= $fileContent;
                $arguments['uses'] .= 'use Livewire\Attributes\Computed;'."\n";
                $arguments['uses'] .= 'use '.$propertyType.';'."\n";
            } elseif ($propertyType == 'boolean') {
                $publicType = 'int';
                $rule = 'nullable|'.$propertyType;
            } elseif ($propertyType == 'date') {
                $publicType = '';
            } elseif ($propertyType == 'datetime') {
                $publicType = '';
            }

            $propertiesString .= "\tpublic ".$publicType.' $'.$propertyName.";\n";
            $validationRules .= "\t\t\t'".$propertyName."' => '".$rule."',\n";
            $loadProperties .= "\t\t\t\$this->".$propertyName.' = $'.$arguments['model_camel_case'].'->'.$propertyName.";\n";
        }

        $arguments['propertiesString'] = rtrim(ltrim($propertiesString, "\t"), "\n");
        $arguments['validationRules'] = rtrim(ltrim($validationRules, "\t"), "\n");
        $arguments['loadProperties'] = rtrim(ltrim($loadProperties, "\t"), "\n");

        unset($arguments['properties']);

        $stubFilePath = ('/stubs/Form.stub');
        $moduleRootPath = realpath($this->getPackageBasePath().$stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            if (gettype($ArgumentValue) != 'string') {
                continue;
            }

            $fileContent = str_replace('{{'.$ArgumentName.'}}', $ArgumentValue, $fileContent);
        }

        return $fileContent;
    }

    private function getFormBladeSkeleton($arguments)
    {
        $modelName = $arguments['model'];
        $className = 'App\\Models\\'.$modelName;
        $model = new $className();

        $content = "<div>\n";
        $content .= "\t<x-form::form wire:submit.prevent=\"{{\$action}}\">\n";

        $safeToEditProperties = array_intersect_key($arguments['properties'], array_flip($arguments['safeProperties']));
        foreach ($safeToEditProperties as $propertyName => $propertyType) {
            $propertyNameStr = Str::of($propertyName)->headline()->lower()->ucfirst()->toString();
            if (Str::contains($propertyType, 'App\\Models\\')) {
                $tableName = (new $propertyType())->getTable();
                $content .= "\t\t".'<x-form::select group-class="mb-3"  :options="$this->'.Str::camel($tableName).'" name="'.$propertyName.'" placeholder="Vyberte" wire:model.blur="'.$propertyName.'" label="{{ __('. $propertyNameStr .") }}\"/>\n";
            } elseif ($propertyType == 'integer') {
                $content .= "\t\t".'<x-form::input group-class="mb-3" type="number" wire:model="'.$propertyName.'" id="'.$propertyName.'" label="{{ __('.$propertyNameStr.") }}\"/>\n";
            } elseif ($propertyType == 'boolean') {
                $content .= "\t\t".'<x-form::checkbox group-class="mb-3" wire:model="'.$propertyName.'" id="'.$propertyName.'" label="{{ __('.$propertyNameStr.") }}\"/>\n";
            } elseif ($propertyType == 'date') {
                $content .= "\t\t".'<x-form::input group-class="mb-3" type="date" wire:model="'.$propertyName.'" id="'.$propertyName.'" label="{{ __('.$propertyNameStr.") }}\"/>\n";
            } elseif ($propertyType == 'datetime') {
                $content .= "\t\t".'<x-form::input group-class="mb-3" type="datetime-local" wire:model="'.$propertyName.'" id="'.$propertyName.'" label="{{ __('.$propertyNameStr.") }}\"/>\n";
            } else {
                $content .= "\t\t".'<x-form::input group-class="mb-3" type="text" wire:model="'.$propertyName.'" id="'.$propertyName.'" label="{{ __('.$propertyNameStr.") }}\"/>\n";
            }
        }

        $content .= "\t\t<x-form::button class=\"btn-primary\" type=\"submit\">@if(\$action == 'update') {{__('Update')}} @else {{__('Create')}} @endif</x-form::button>\n";
        $content .= "\t</x-form::form>\n";
        $content .= '</div>';

        return $content;
    }

    private function getControllerSkeleton(array $arguments) // model (User), model_name (user)
    {
        $stubFilePath = ('/stubs/controllers.stub');
        $moduleRootPath = realpath($this->getPackageBasePath().$stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            if (gettype($ArgumentValue) != 'string') {
                continue;
            }

            $fileContent = str_replace('{{'.$ArgumentName.'}}', $ArgumentValue, $fileContent);
        }

        return $fileContent;
    }
}
