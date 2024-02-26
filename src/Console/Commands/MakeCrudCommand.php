<?phpdaša simkova

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

    protected function getBasePath(){
        return  __DIR__ . '/../../..';
    }
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

            $content = $this->GetFormClassSkeleton([
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
        $arguments['model_lower_case'] = strtolower($arguments['model']);
        $arguments['headers'] =  implode('","', $arguments['headers']);

        $stubFilePath = ('/stubs/DataTable.stub');
        $moduleRootPath = realpath($this->getBasePath() . $stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            $fileContent = str_replace("{{" . $ArgumentName . "}}", $ArgumentValue, $fileContent);
        }
        return $fileContent;
    }

    private function GetFormClassSkeleton(array $arguments)
    {
        $arguments['model_lower_case'] = strtolower($arguments['model']);

        $stubFilePath = ('/stubs/Form.stub');
        $moduleRootPath = realpath($this->getBasePath() . $stubFilePath);

        $fileContent = file_get_contents($moduleRootPath, true);
        foreach ($arguments as $ArgumentName => $ArgumentValue) {
            $fileContent = str_replace("{{" . $ArgumentName . "}}", $ArgumentValue, $fileContent);
        }
        return $fileContent;
    }

    private function GetFormBladeSkeleton($arguments)
    {
        $modelName = $arguments['model'];
        $className = 'App\\Models\\' . $modelName;
        $model = new $className();

        $content = "<div>\n";
        $content .= "\t<x-form::form wire:submit.prevent=\"store\">\n";

        foreach ($model->getFillable() as $name) {
            $LVModelName = strtolower($modelName . "." . $name);
            $content .= "\t\t" . "\<x-form::input group-class=\"mb-3\" type=\"text\" wire:model=\"" . $LVModelName . "\" id=\"" . $LVModelName . "\" label=\"" . $LVModelName . "\"/>\n";
        }

        $content .= "\t\t<x-form::button class=\"btn-primary\" type=\"submit\">" . __('Create') . "</x-form::button>\n";
        $content .= "\t</x-form::form>\n";
        $content .= "</div>";
        
        return $content;
    }
}
