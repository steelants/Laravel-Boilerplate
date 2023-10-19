<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

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
        self::updatePackagesJson();

        self::exportStubs('views');
        self::exportStubs('js');
        self::exportStubs('sass');
        self::updateVite();
        self::removeNodeModules();
        $this->components->warn('Please run [npm install && npm run build] to compile your fresh scaffolding.');
    }

    protected static function updatePackagesJson()
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages["dependencies"]["sass"] = '^1.56.1';
        $packages["dependencies"]["bootstrap"] = '^5.2.3';
        $packages["dependencies"]["@fortawesome/fontawesome-free"] = '^5.15.4';
        $packages["dependencies"]["@popperjs/core"] = '^2.11.6';
        $packages["dependencies"]["vite"] = "^4.0.0";
        $packages["dependencies"]["laravel-vite-plugin"] = "^0.8.0";

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

    protected function exportStubs($type = "views")
    {
        $baseDir = __DIR__ . '/../../..';
        $moduleSubPath = ('/stubs/resources/' . $type);
        $laravelSubPath = ('/resources/' . $type);
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

    protected function updateVite(){
        #TODO: Include rather than override
        $baseDir = realpath(__DIR__ . '/../../../stubs');
        copy($baseDir.'/vite.config.js', base_path('vite.config.js'));
    }
}
