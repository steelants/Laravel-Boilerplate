<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {model}
                            {--force : Overwrite existing files by default}
							{--full-page-components : Make form full page}';// {--view : Generate controller and blade files}

    protected $description = 'Creates CRUD for specified Command';

    protected function getPackageBasePath()
    {
        return  __DIR__ . '/../../..';
    }
    public function handle(): void
    {
        $model = ucfirst($this->argument('model'));
        if (!class_exists('App\\Models\\' . $model)) {
            $this->components->error($model . ' model not Found!');
            return;
        }
        $modelObject = (new ('App\\Models\\' . $model));
        $fillables = $modelObject->getFillable();
        if ($fillables == []) {
            $this->components->warn('Please make shure that $fillable variable of model ' . $model . ' is defined correctly.');
        }
        $casts = $modelObject->getCasts();
        $properties = [];
        foreach ($fillables as $fillable) {
            $modelClassName = ucfirst(Str::camel(Str::replace('_id', '', $fillable, false)));
            $finalCast = 'string';
            if (
                Str::contains($fillable, '_id', true) &&
                class_exists('App\\Models\\' . $modelClassName)
            ) {
                if (method_exists($modelObject,Str::camel($modelClassName))) {
                    $finalCast = 'App\\Models\\' . $modelClassName;
                }
            } elseif (isset($casts[$fillable])) {
                $finalCast = $casts[$fillable];
            }
            $properties[$fillable] = $finalCast;
        }

        $this->makeClassFile('app/Livewire/' . $model, "Form.php", $model, $properties);
        $this->makeClassFile('app/Livewire/' . $model, "DataTable.php", $model, $properties);
		$this->makeClassFile('app/Http/Controllers', $model . "Controller.php", $model, $properties);
    }

    private function makeClassFile(string $path, string $fileName, string $model, array $properties)
    {
        $testFilePath = base_path() . '/' . $path . '/' . $fileName;
        if (file_exists($testFilePath) && !$this->option('force')) {
            if (!$this->components->confirm("The [" . $testFilePath . "] test already exists. Do you want to replace it?")) {
                return;
            }
        }

        $folderpath = str_replace('/' . $fileName, "", $testFilePath);
        if (!file_exists($folderpath)) {
            mkdir($folderpath, 0777, true);
        }

		$namespace = Str::replace("/", '\\', Str::afterLast($path, "/Controllers"));

        $this->components->info("creating File: " . $testFilePath);
        if ($fileName == "Form.php") {
            Artisan::call('make:livewire ' . $model . '.Form --force');

            $content = $this->getFormClassSkeleton([
                'model'   => $model,
                'properties' => $properties,
				'action_back' => $this->option('full-page-components') ? '$this->redirectRoute(\'' . (Str::contains($namespace, '/Controllers') ? Str::replace("\\", ".", $namespace) . "." : "") . Str::snake($model, ".") . '.index' . '\');' : '',
            ]);
            file_put_contents($testFilePath, $content);

            $bladePathFile = explode("/app", (str_replace('/' . $fileName, "", $testFilePath)))[0];

            $bladePathFile = $bladePathFile . "/resources/views/livewire/" . Str::snake($model, "-") . "/form.blade.php";
            $modaltcontent = $this->getFormBladeSkeleton([
                'model' => $model,
                'properties' => $properties
            ]);

            file_put_contents($bladePathFile, $modaltcontent);
        } elseif ($fileName == "DataTable.php") {
            $content = $this->getDataTableClassSkeleton([
                'model'   => $model,
                'headers' => array_keys($properties),
            ]);
            file_put_contents($testFilePath, $content);
        } elseif (Str::contains($fileName, "Controller.php")) {
			$model_name = Str::lower($model);
			$content = $this->getControllerSkeleton([
				'namespace' => (!empty($namespace) ? "\\" . $namespace : ""),
                'model' => $model,
                'model_name' => $model_name,
				'trait' => $this->option('full-page-components') ? 'FullPageCRUD' : 'CRUD',
            ]);
            file_put_contents($testFilePath, $content);

			$routesPathFile = explode("/app", (str_replace('/' . $fileName, "", $testFilePath)))[0];

			//index route
			if (!Route::has(Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index')) {
				$this->components->info("creating Route: " . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index');
				$route = "Route::get('/" . $model_name . "', [App\Http\Controllers\\" . $model . "Controller::class, 'index'])->name('" . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index' . "');";
            	$pathFile = $routesPathFile . "/routes/web.php";
				file_put_contents($pathFile, $route, FILE_APPEND);
			} else {
				$this->components->info("Route exists: " . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index');
			}

			if ($this->option('full-page-components')) {
				//form route
				if (!Route::has(Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.form')) {
					$this->components->info("creating Route: " . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.form');
					$route = "\r\nRoute::get('/" . $model_name . "/form/{modelId?}', [App\Http\Controllers\\" . $model . "Controller::class, 'form'])->name('" . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.form' . "');";
					$pathFile = $routesPathFile . "/routes/web.php";
					file_put_contents($pathFile, $route, FILE_APPEND);
				} else {
					$this->components->info("Route exists: " . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.form');
				}
			}
		}
    }

    private function getDataTableClassSkeleton(array $arguments)
    {
        $arguments['model_camel_case'] = Str::camel($arguments['model']);
        $arguments['model_snake_case'] = Str::snake($arguments['model_camel_case']);
        $arguments['model_snake_case_dash'] = Str::snake($arguments['model_camel_case'], "-");

        $headerProperties = "";
        foreach ($arguments['headers'] as $key => $header) {
            $headerProperties .= "\t\t\t'" . $header . "' => '" . $header . "',\n";
        }
        $arguments['headerProperties'] = rtrim(ltrim($headerProperties, "\t"), "\n");
        unset($arguments['headers']);

        $stubFilePath = $this->option('full-page-components') ? ('/stubs/FullPageDataTable.stub') : ('/stubs/DataTable.stub');
        $moduleRootPath = realpath($this->getPackageBasePath() . $stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            if (gettype($ArgumentValue) != 'string') {
                continue;
            }

            $fileContent = str_replace("{{" . $ArgumentName . "}}", $ArgumentValue, $fileContent);
        }
        return $fileContent;
    }

    private function getFormClassSkeleton(array $arguments)
    {
        $arguments['model_camel_case'] = Str::camel($arguments['model']);
        $arguments['model_snake_case'] = Str::snake($arguments['model_camel_case'], '-');

        $arguments['uses'] = "";
        $arguments['modelFunctions'] = "";
        $propertiesString = "";
        $validationRules = "";
        $loadProperties = "";

        foreach ($arguments['properties'] as $propertyName => $propertyType) {
            $rule = "required|" . $propertyType;
            $publicType = $propertyType;
            if (Str::contains($propertyType, "App\\Models\\")) {
                $tableName = (new $propertyType)->getTable();
                $rule = 'required|exists:' . $tableName . ',id';
                $publicType = '';

                $stubFilePath = ('/stubs/ModelForm.stub');
                $moduleRootPath = realpath($this->getPackageBasePath() . $stubFilePath);
                $fileContent = file_get_contents($moduleRootPath, true);
                $modelArguments = [
                    'model_camel_case_plural' => Str::camel($tableName),
                    'model' => Str::afterLast($propertyType, "Models\\"),
                ];
                foreach ($modelArguments as $ArgumentName => $ArgumentValue) {
                    if (gettype($ArgumentValue) != 'string') {
                        continue;
                    }

                    $fileContent = str_replace("{{" . $ArgumentName . "}}", $ArgumentValue, $fileContent);
                }
                $arguments['modelFunctions'] .= $fileContent;
                $arguments['uses'] .= 'use Livewire\Attributes\Computed;' . "\n";
                $arguments['uses'] .= 'use ' . $propertyType . ';' . "\n";
            } elseif ($propertyType == "boolean") {
                $publicType = 'int';
                $rule = "nullable|" . $propertyType;
            } elseif ($propertyType == "date") {
                $publicType = '';
            } elseif ($propertyType == "datetime") {
                $publicType = '';
            }
            $propertiesString .= "\tpublic " . $publicType . " $" . $propertyName . ";\n";
            $validationRules .= "\t\t\t'" . $propertyName . "' => '" . $rule . "',\n";
            $loadProperties .= "\t\t\t\$this->" . $propertyName . " = $" . $arguments['model_camel_case'] . "->".$propertyName.";\n";
        }

        $arguments['propertiesString'] = rtrim(ltrim($propertiesString, "\t"), "\n");
        $arguments['validationRules'] = rtrim(ltrim($validationRules, "\t"), "\n");
        $arguments['loadProperties'] = rtrim(ltrim($loadProperties, "\t"), "\n");

        unset($arguments['properties']);

        $stubFilePath = ('/stubs/Form.stub');
        $moduleRootPath = realpath($this->getPackageBasePath() . $stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            if (gettype($ArgumentValue) != 'string') {
                continue;
            }

            $fileContent = str_replace("{{" . $ArgumentName . "}}", $ArgumentValue, $fileContent);
        }
        return $fileContent;
    }

    private function getFormBladeSkeleton($arguments)
    {
        $modelName = $arguments['model'];
        $className = 'App\\Models\\' . $modelName;
        $model = new $className();

        $content = "<div>\n";
        $content .= "\t<x-form::form wire:submit.prevent=\"{{\$action}}\">\n";

        foreach ($model->getFillable() as $name) {
            $propertyName = $name;
            $propertyType = $arguments['properties'][$name];
            if (Str::contains($propertyType, "App\\Models\\")) {
                $tableName = (new $propertyType)->getTable();
                $content .= "\t\t" . "<x-form::select group-class=\"mb-3\"  :options=\"\$this->" . Str::camel($tableName) . "\" name=\"" . $propertyName . "\" placeholder=\"Vyberte\" wire:model.blur=\"" . $propertyName . "\" label=\"" . $propertyName . "\"/>\n";
            } elseif ($propertyType == "integer") {
                $content .= "\t\t" . "<x-form::input group-class=\"mb-3\" type=\"number\" wire:model=\"" . $propertyName . "\" id=\"" . $propertyName . "\" label=\"" . $propertyName . "\"/>\n";
            } elseif ($propertyType == "boolean") {
                $content .= "\t\t" . "<x-form::checkbox group-class=\"mb-3\" wire:model=\"" . $propertyName . "\" id=\"" . $propertyName . "\" label=\"" . $propertyName . "\"/>\n";
            } elseif ($propertyType == "date") {
                $content .= "\t\t" . "<x-form::input group-class=\"mb-3\" type=\"date\" wire:model=\"" . $propertyName . "\" id=\"" . $propertyName . "\" label=\"" . $propertyName . "\"/>\n";
            } elseif ($propertyType == "datetime") {
                $content .= "\t\t" . "<x-form::input group-class=\"mb-3\" type=\"datetime-local\" wire:model=\"" . $propertyName . "\" id=\"" . $propertyName . "\" label=\"" . $propertyName . "\"/>\n";
            } else {
                $content .= "\t\t" . "<x-form::input group-class=\"mb-3\" type=\"text\" wire:model=\"" . $propertyName . "\" id=\"" . $propertyName . "\" label=\"" . $propertyName . "\"/>\n";
            }
        }

        $content .= "\t\t<x-form::button class=\"btn-primary\" type=\"submit\">@if(\$action == 'update') {{__('boilerplate::ui.save')}} @else {{__('boilerplate::ui.create')}} @endif</x-form::button>\n";
        $content .= "\t</x-form::form>\n";
        $content .= "</div>";

        return $content;
    }

	private function getControllerSkeleton(array $arguments) //model (User), model_name (user)
    {
        $stubFilePath = ('/stubs/controllers.stub');
        $moduleRootPath = realpath($this->getPackageBasePath() . $stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            if (gettype($ArgumentValue) != 'string') {
                continue;
            }

            $fileContent = str_replace("{{" . $ArgumentName . "}}", $ArgumentValue, $fileContent);
        }
        return $fileContent;
    }
}
