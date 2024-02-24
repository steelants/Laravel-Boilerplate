<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use App\Models;
//

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud {model}';

    protected $description = 'Creates CRUD for specified Command';

    public function handle(): void
    {
        $model = $this->argument('model');
        if (!class_exists('App\\Models\\' . $model)) {
            $this->components->error('Model not Found!');
            return;
        }

        $this->makeClassFile('app/Http/Livewire/Components/' . $model, "Form.php", $model);
        $this->makeClassFile('app/Http/Livewire/Components/' . $model, "DataTable.php", $model);

        Artisan::call('livewire:discover');
    }

    private function makeClassFile(string $path, string $fileName, string $model)
    {
        $testFilePath = base_path() . '/' . $path . '/components/' . $fileName;
        if (file_exists($testFilePath)) {
            if (!$this->components->confirm("The [" . $testFilePath . "] test already exists. Do you want to replace it?")) {
                return;
            }
        }

        $folderpath = str_replace('/' . $fileName, "", $testFilePath);
        if (!file_exists($folderpath)) {
            mkdir($folderpath, 0777, true);
        }

        $this->components->info("creting File" . $testFilePath);
        if ($fileName == "Form.php") {
            Artisan::call('make:livewire Components.' . $model . '.Form --force');
            $content = $this->GetModalClassSkeleton([
                'model' => $model,
                'headers' => (new ('App\\Models\\' . $model))->getFillable(),
            ]);
            file_put_contents($testFilePath, $content);

            $bladePathFile = explode("/app", (str_replace('/' . $fileName, "", $testFilePath)))[0];
            $bladePathFile = $bladePathFile . "/resources/views/livewire/components/source/form.blade.php";
            $modaltcontent = $this->GetFormBladeSkeleton([
                'model' => $model,
            ]);
            file_put_contents($bladePathFile, $modaltcontent);
        } elseif ($fileName == "DataTable.php") {
            $content = $this->GetDataTableClassSkeleton([
                'model' => $model,
                'headers' => (new ('App\\Models\\' . $model))->getFillable(),
            ]);
            file_put_contents($testFilePath, $content);
        }
    }

    private function GetDataTableClassSkeleton(array $arguments)
    {
        return '<?php
namespace App\\Http\\Livewire\\Components\\' . $arguments['model'] . ';

use App\\Models\\' . $arguments['model'] . ';
use SteelAnts\\DataTable\Http\\Livewire\DataTableV2;
use Illuminate\\Database\Eloquent\\Builder;

class DataTable extends DataTableV2
{
    public $listeners = [
        \'' . strtolower($arguments['model']) . 'Added\' => \'$refresh\'
        \'closeModal\' => \'$refresh\'
    ];

    public function query(): Builder
    {
        return ' . $arguments['model'] . '::query();
    }

    public function headers(): array
    {
        return ["id", "' . implode('","', $arguments['headers']) . '", "actions"];
    }

    public function remove($' . strtolower($arguments['model']) . '_id){
        ' . $arguments['model'] . '::find($' . strtolower($arguments['model']) . '_id)->delete();
    }
}';
    }

    private function GetModalClassSkeleton(array $arguments)
    {
        $modelName = $arguments['model'];
        $className = 'App\\Models\\' . $modelName;
        $model = new $className();

        return '<?php
namespace App\\Http\\Livewire\\Components\\' . $arguments['model'] . ';

use Livewire\\Component;
use App\\Models\\' . $arguments['model'] . ';

class Form extends Component
{
    public ' . $arguments['model'] . ' $' . strtolower($arguments['model']) . ';

    protected function rules()
    {
        return [];
    }

    public function mount (' . $arguments['model'] . ' $' . strtolower($arguments['model']) . '){
        $this->' . strtolower($arguments['model']) . ' = $' . strtolower($arguments['model']) . ';
    }

    public function store()
    {
        //$this->validate();
        $this->' . strtolower($arguments['model']) . '->save();
        $this->emit(\'closeModal\');
    }

    public function render()
    {
        return view(\'livewire.components.' . strtolower($arguments['model']) . '.form\');
    }

}';
    }

    private function GetFormBladeSkeleton($arguments)
    {
        $modelName = $arguments['model'];
        $className = 'App\\Models\\' . $modelName;
        $model = new $className();
        $content = "<div>\n";
        $content .= "\t<x-form livewireAction=\"store\" method=\"post\" enctype=\"multipart/form-data\">\n";

        foreach ($model->getFillable() as $name) {
            $LVModelName = strtolower($modelName . "." . $name);
            $content .= "\t\t" . '\<x-form-input livewireModel="' . $LVModelName . '" type="text" id="' . $LVModelName . '" name="' . $LVModelName . '" label="' . $LVModelName . '" />\n';
        }

        $content .= "\t\t<input type=\"submit\" value=\"submit\" />\n";
        $content .= "\t</x-form>\n";
        $content .= "</div>\n";
        return $content;
    }
}