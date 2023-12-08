<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use App\Models;

class MakeCrudCommand extends Command
{
    protected $signature = 'boilerplate:make-crud {model}';

    protected $description = 'Creates CRUD for specified Command';

    public function handle(): void
    {
        $model = $this->argument('model');
        if (!class_exists('App\\Models\\'.$model)) {
            $this->components->error('Model not Found!');
            return;
        }

        $this->makeClassFile('app/Http/Livewire/' . $model, "Form.php", $model);
        $this->makeClassFile('app/Http/Livewire/' . $model, "DataTable.php", $model);

        Artisan::call('livewire:discover');
    }

    private function makeClassFile(string $path,string $fileName, string $model){
        $testFilePath = base_path() . '/'. $path .'/' . $fileName;
        if (file_exists($testFilePath)) {
            if (!$this->components->confirm("The [" . $testFilePath . "] test already exists. Do you want to replace it?")) {
                return;
            }
        }

        $folderpath = str_replace('/'.$fileName, "", $testFilePath);
        if (!file_exists($folderpath)) {
            mkdir($folderpath, 0777, true);
        }

        $this->components->info("creting File" . $testFilePath);
        if ($fileName == "Form.php"){
            Artisan::call('make:livewire ' . $model . '.Form');
        } elseif($fileName == "DataTable.php") {
            $content = $this->GetDataTableClassSkeleton([
                'model' => $model
            ]);
            file_put_contents($testFilePath, $content);
        }


    }

    private function GetDataTableClassSkeleton(array $arguments){
        return '<?php
namespace App\\Http\\Livewire\\'.$arguments['model'].';

use App\\Models\\'.$arguments['model'].';
use SteelAnts\\DataTable\Http\\Livewire\DataTableV2;
use Illuminate\\Database\Eloquent\\Builder;

class DataTable extends DataTableV2
{
    public $listeners = [
        \''.strtolower($arguments['model']).'Added\' => \'$refresh\'
    ];

    public function query(): Builder
    {
        return '.$arguments['model'].'::query();
    }

    public function headers(): array
    {
        return ["id", "actions"];
    }


    public function remove($'.strtolower($arguments['model']).'_id){
        '.$arguments['model'].'::find($'.strtolower($arguments['model']).'_id)->delete();
    }
}';
    }
}