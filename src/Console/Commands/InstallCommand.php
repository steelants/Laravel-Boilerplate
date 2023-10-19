<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'boilerplate:install';

    protected $description = 'Install Boilerplate';

    public function handle(): void
    {
        self::overrideViews();
        self::updatePackagesJson();
        self::removeNodeModules();
        $this->components->warn('Please run [npm install && npm vite build] to compile your fresh scaffolding.');
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

    protected function overrideViews()
    {
        $baseDir = __DIR__ . '/../../..';
        $moduleSubPath = '/stubs/resources/views';
        $laravelSubPath = '/resources/views';

        $viewsStubs = array_diff(scandir($baseDir .  $moduleSubPath), array('..', '.'));

        foreach ($viewsStubs as $view) {
            $viewPathLaravelPackage = realpath($baseDir .  $moduleSubPath . "/" . $view);
            if (file_exists($viewPathLaravelPackage)) {
                if (!$this->components->confirm("The [" . $laravelSubPath . '/' . $view . "] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy($viewPathLaravelPackage, base_path($laravelSubPath . '/' . $view));
        }
    }
}
