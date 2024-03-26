<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'boilerplate:install
                            {--force : Overwrite existing views by default}';

    protected $description = 'Install Boilerplate';

    public function handle(): void
    {
        $this->components->info('Installing Boilerplate Scaffolding');

        self::exportPrefabs('app'); //Add prefabs for controllers
        self::exportPrefabs('database/migrations');
        self::exportPrefabs('resources/views');
        self::exportPrefabs('resources/js');
        self::exportPrefabs('resources/sass');
        self::exportPrefabs('storage/app/public');
        self::exportPrefabs('config');


        self::updatePackagesJson();
        self::updateVite();
        self::removeNodeModules();

        $this->components->info('Adding Routes');
        self::appendRoutes();

        $function = "\n";
        $function .= "    protected function context(): array\n";
        $function .= "    {\n";
        $function .= "        return array_merge(parent::context(), [\n";
        $function .= "            'current_url' => request()->url(),\n";
        $function .= "        ]);\n";
        $function .= "    }\n";

        if ($this->addClassFunction(base_path('/app/Exceptions/Handler.php'), $function, 'context()')) {
            $this->components->info('Modifying App\Exceptions\Handler');
        } else {
            $this->components->error('Unable to modify App\Exceptions\Handler');
        }

        $this->components->warn('Cleaning Cashes');
        
        Artisan::call('storage:link');
        Artisan::call('livewire:discover');
        Artisan::call('optimize:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->components->warn('Please run [npm install && npm run build] to compile your fresh scaffolding.');
    }

    protected static function updatePackagesJson()
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages["dependencies"]["jquery"] = '^3.6.1';
        $packages["dependencies"]["sass"] = '^1.56.1';
        $packages["dependencies"]["bootstrap"] = '^5.3';
        $packages["dependencies"]["@fortawesome/fontawesome-free"] = '^5.15.4';
        $packages["dependencies"]["@popperjs/core"] = '^2.11.6';
        $packages["dependencies"]["vite"] = '^4.0.0';
        $packages["dependencies"]["laravel-vite-plugin"] = '^0.8.0';
        $packages["dependencies"]["quill"] = "2.0.0-rc.2";
        $packages["dependencies"]["quill-table-ui"] = "^1.0.7";

        file_put_contents(base_path('package.json'), json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL);
    }

    protected static function removeNodeModules()
    {
        if (!file_exists(base_path('node_modules'))) {
            return;
        }

        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    protected function exportPrefabs($type = "views")
    {
        $baseDir = __DIR__ . '/../../..';
        $moduleSubPath = ('/prefabs/' . $type);
        $laravelSubPath = ('/' . $type);
        $moduleRootPath = realpath($baseDir . $moduleSubPath);

        foreach (File::allFiles($moduleRootPath) as $file) {
            $laravelViewRoot = str_replace($moduleRootPath, $laravelSubPath, $file->getPath());
            $stubFullPath = ($file->getPath() . "/" . $file->getFilename());
            $viewFullPath = (base_path($laravelViewRoot) . "/" . $file->getFilename());

            $this->checkDirectory(dirname($viewFullPath));

            if (file_exists($viewFullPath) && !$this->option('force')) {
                if (!$this->components->confirm("The [" . $laravelViewRoot . '/' . $file->getFilename() . "] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy($stubFullPath, $viewFullPath);
        }
    }

    protected function checkDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function updateVite()
    {
        #TODO: Include rather than override
        $baseDir = realpath(__DIR__ . '/../../../prefabs');
        copy($baseDir . '/vite.config.js', base_path('vite.config.js'));
    }

    protected function appendRoutes(string $RouteType = "web")
    {
        $baseDir = realpath(__DIR__ . '/../../../stubs');
        $RouteFilePath = base_path('routes/' . $RouteType . '.php');

        if (strpos(file_get_contents($RouteFilePath), 'Route::Auth();') !== false) {
            return;
        }
        
        if (strpos(file_get_contents($RouteFilePath), 'Route::auth();') !== false) {
            return;
        }

        file_put_contents($RouteFilePath, file_get_contents($baseDir  . '/routes.stub'), FILE_APPEND);
    }

    protected function addClassFunction(string $filePath, string  $functionCode, string $functionName)
    {
        $ClassFileContent = file_get_contents($filePath);
        if (str_contains($ClassFileContent, $functionName)) {
            return true;
        }

        preg_match_all('/\}/', $ClassFileContent, $matches, PREG_OFFSET_CAPTURE);
        if (empty($matches[0])) {
            return false;
        }

        $pos = end($matches[0])[1];
        $ModifiedContent = substr_replace($ClassFileContent, $functionCode, $pos, 0);
        return file_put_contents($filePath, $ModifiedContent);
    }
}
