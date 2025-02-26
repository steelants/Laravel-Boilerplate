<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {model}
                            {--force : Overwrite existing files by default}'; // {--view : Generate controller and blade files}

    protected $description = 'Creates CRUD for specified Command';

    protected function getPackageBasePath()
    {
        return  __DIR__ . '/../../..';
    }
    public function handle(): void
    {
        $model = ucfirst($this->argument('model'));
        if (!class_exists('App\\Models\\' . $model)) {
            $this->components->error('Model not Found!');
            return;
        }

        $fillable = (new ('App\\Models\\' . $model))->getFillable();
        if ($fillable == []) {
            $this->components->warn('Please make shure that $fillable variable of model ' . $model . ' is defined correctly.');
        }

        $this->makeClassFile('app/Livewire/' . $model, "Form.php", $model, $fillable);
        $this->makeClassFile('app/Livewire/' . $model, "DataTable.php", $model, $fillable);
		$this->makeClassFile('app/Http/Controllers', $model . "Controller.php", $model, $fillable);
    }

    private function makeClassFile(string $path, string $fileName, string $model, array $fillable)
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

        $this->components->info("creating File: " . $testFilePath);
        if ($fileName == "Form.php") {
            Artisan::call('make:livewire ' . $model . '.Form --force');

            $content = $this->getFormClassSkeleton([
                'model'   => $model,
                'headers' => $fillable,
            ]);
            file_put_contents($testFilePath, $content);

            $bladePathFile = explode("/app", (str_replace('/' . $fileName, "", $testFilePath)))[0];

            $bladePathFile = $bladePathFile . "/resources/views/livewire/" . Str::snake($model, "-") . "/form.blade.php";
            $modaltcontent = $this->getFormBladeSkeleton(['model' => $model]);

            file_put_contents($bladePathFile, $modaltcontent);
        } elseif ($fileName == "DataTable.php") {
            $content = $this->getDataTableClassSkeleton([
                'model'   => $model,
                'headers' => $fillable,
            ]);
            file_put_contents($testFilePath, $content);
        } elseif (Str::contains($fileName, "Controller.php")) {
			$model_name = Str::lower($model);
			$namespace = Str::replace("/", '\\', Str::afterLast($path, "/Controllers"));
			$content = $this->getControllerSkeleton([
				'namespace' => (!empty($namespace) ? "\\" . $namespace : ""),
                'model' => $model,
                'model_name' => $model_name,
            ]);
            file_put_contents($testFilePath, $content);

			if (!Route::has(Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index')) {
				$this->components->info("creating Route: " . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index');
				$routesPathFile = explode("/app", (str_replace('/' . $fileName, "", $testFilePath)))[0];
				$route = "Route::get('/" . $model_name . "', [App\Http\Controllers\\" . $model . "Controller::class, 'index'])->name('" . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index' . "');";
            	$routesPathFile = $routesPathFile . "/routes/web.php";
				file_put_contents($routesPathFile, $route, FILE_APPEND);
			} else {
				$this->components->info("Route exists: " . Str::replace("\\", ".", $namespace) . (!empty($namespace) ? "." : "") . Str::snake($model, ".") . '.index');
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

        $stubFilePath = ('/stubs/DataTable.stub');
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

        $propertiesString = "";
        $validationRules = "";
        $loadProperties = "";

        foreach ($arguments['headers'] as $key => $header) {
            $propertiesString .= "\tpublic string $" . $header . " = '';\n";
            $validationRules .= "\t\t\t'" . $header . "' => 'required',\n";
            $loadProperties .= "\t\t\t\$this->" . $header . " = $" . $arguments['model_camel_case'] . "->".$header.";\n";
        }

        $arguments['properties'] = rtrim(ltrim($propertiesString, "\t"), "\n");
        $arguments['validationRules'] = rtrim(ltrim($validationRules, "\t"), "\n");
        $arguments['loadProperties'] = rtrim(ltrim($loadProperties, "\t"), "\n");

        unset($arguments['headers']);

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
            $LVModelName = strtolower($name);
            $content .= "\t\t" . "<x-form::input group-class=\"mb-3\" type=\"text\" wire:model=\"" . $LVModelName . "\" id=\"" . $LVModelName . "\" label=\"" . $LVModelName . "\"/>\n";
        }

        $content .= "\t\t<x-form::button class=\"btn-primary\" type=\"submit\">@if(\$action == 'update') {{__('boulerplate::ui.save')}} @else {{__('boulerplate::ui.create')}} @endif</x-form::button>\n";
        $content .= "\t</x-form::form>\n";
        $content .= "</div>";

        return $content;
    }

	private function getControllerSkeleton(array $arguments) //model (User), model_name (user)
    {
        $stubFilePath = ('/stubs/Controllers.stub');
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
