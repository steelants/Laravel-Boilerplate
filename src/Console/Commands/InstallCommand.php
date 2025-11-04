<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Throwable;

class InstallCommand extends Command
{
    protected $signature = 'install:boilerplate
                            {--force : Overwrite existing files by default}
                            {--views-only : Overwrite existy views only}
                            {--no-migration : Install boilerplate without running migrations}';

    protected $description = 'Install Boilerplate';

    public function handle(): void
    {
        $this->components->info('Installing Auth Scaffolding');

        $baseDir = realpath(__DIR__ . '/../../../stubs');
        $RouteFilePath = base_path('routes/web.php');

        if (strpos(file_get_contents($RouteFilePath), 'Route::auth();') === false) {
            //If authentication not installed install
            if (strpos(file_get_contents('bootstrap/app.php'), "api: __DIR__.'/../routes/api.php',") === false) {
                $this->call('install:api', ['--without-migration-prompt' => true, '--force' => $this->option('force')]);
            }

            $this->call('install:auth', ['--force' => $this->option('force')]);
            //Artisan::call('install:auth --force');
            file_put_contents($RouteFilePath, str_replace('Route::auth();', '', file_get_contents($RouteFilePath)));
        }

        $this->components->info('Installing Boilerplate Scaffolding');

        if (!$this->option('views-only')) {
            self::exportPrefabs('app'); //Add prefabs for controllers
        }

        self::exportPrefabs('resources/views');
        self::exportPrefabs('resources/js/boilerplate');
        self::appendJS();
        self::exportPrefabs('resources/sass/boilerplate');
        self::appendSASS();
        self::exportPrefabs('storage');

        if (!$this->option('views-only')) {
            self::exportPrefabs('config');

            $this->components->info('Installing Boilerplate Scaffolding');

            self::updatePackagesJson();
            self::updateVite();
            self::removeNodeModules();

            $this->components->info('Adding Routes');
            self::appendRoutes();
            $this->components->info('Adding Exceptions');
            self::appendExceptions();

            if (!$this->option('no-migration')) {
                $this->components->warn('Running Migrations');
                Artisan::call('migrate');
            }
        }

        $this->components->warn('Cleaning Cashes');
        Artisan::call('storage:link');
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

		unset($packages["devDependencies"]);
        $packages["dependencies"]["jquery"] = '^3.6.1';
        $packages["dependencies"]["sass"] = '1.77.6';
        $packages["dependencies"]["bootstrap"] = '^5.3';
        $packages["dependencies"]["@fortawesome/fontawesome-free"] = '^5.15.4';
        $packages["dependencies"]["@popperjs/core"] = '^2.11.6';
        $packages["dependencies"]["vite"] = '^6.2.0';
        $packages["dependencies"]["laravel-vite-plugin"] = '^1.0.0';
        $packages["dependencies"]["quill"] = "^2.0.3";
        $packages["dependencies"]["quill-magic-url"] = "^4.2.0";
        $packages["dependencies"]["quill-mention"] = "^6.0.2";
        $packages["dependencies"]["quill-table-ui"] = "^1.0.7";

        file_put_contents(base_path('package.json'), json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL);
    }

    protected static function removeNodeModules()
    {
        if (!file_exists(base_path('node_modules'))) {
            return;
        }

        tap(new Filesystem(), function ($files) {
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
		$HashFilePath = (storage_path() . DIRECTORY_SEPARATOR . 'boilerplate_install.json');
		$checkSums = [];

        if (File::exists($HashFilePath)) {
		    $checkSums = (json_decode(file_get_contents($HashFilePath), true) ?? []);
        }

        foreach (File::allFiles($moduleRootPath) as $file) {
            try {
                $laravelViewRoot = str_replace($moduleRootPath, $laravelSubPath, $file->getPath());
                $stubFullPath = ($file->getPath() . "/" . $file->getFilename());
                $viewFullPath = (base_path($laravelViewRoot) . "/" . $file->getFilename());

				$fileHash  = null;
				if (file_exists($viewFullPath)) {
					$fileHash =  hash_file('sha256', $viewFullPath) ?? null;
				}

                $this->checkDirectory(dirname($viewFullPath));

				//TODO: Verifi hash of file
				$FileWasCustomized = true;
				if (isset($checkSums[$laravelViewRoot]) && $checkSums[$laravelViewRoot] == $fileHash)	{
					$FileWasCustomized = false;
				}

				if (file_exists($viewFullPath) && (!$this->option('force') | $FileWasCustomized)) {
					$message = "The [" . $laravelViewRoot . '/' . $file->getFilename() . "] ".PHP_EOL." file already exists. Do you want to replace it?";
					if ($FileWasCustomized) {
						$message = "The [" . $laravelViewRoot . '/' . $file->getFilename() . "] ".PHP_EOL." file was customized. Do you want to replace it?";
					}
					if (!$this->option('force') && !$this->components->confirm($message)) {
						continue;
					}
				}

                copy($stubFullPath, $viewFullPath);
				$checkSums[$laravelViewRoot] = $fileHash;
				$message = "File: " . $viewFullPath . ' / '. PHP_EOL. " Replace With: ". $stubFullPath;
                $this->components->info($message);
                //code...
            } catch (Throwable $th) {
                $this->components->error($th->getMessage());
            } finally {
				file_put_contents($HashFilePath, json_encode($checkSums, JSON_PRETTY_PRINT));
			}
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

    protected function addClassFunction(string $filePath, string $functionCode, string $functionName)
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

    protected static function boilerplateString(string $text, string $name)
    {
        return sprintf("/* BOILERPLATE $name */\n// Remove surrounding coments if customization code below is needed !!!\n%s\n/* BOILERPLATE $name */", $text);
    }

    protected static function appendFile(string $filepath, string $stub, string $searchWord)
    {
        $baseDir = realpath(__DIR__ . '/../../../stubs');
        $content = file_get_contents($filepath);
        $name = str_replace('.stub', '', $stub);
        $newContent = self::boilerplateString(file_get_contents($baseDir . DIRECTORY_SEPARATOR . $stub), $name);

        $pattern = '/\/\* BOILERPLATE ' . $name . ' \*\/\s*\/\/ Remove surrounding coments if customization code below is needed !!!\s*(.*?)\s*\/\* BOILERPLATE ' . $name . ' \*\//s';

        if (strpos($content, $newContent) !== false) {
            return;
        }

        if (strpos($content, $searchWord) === false) {
            return;
        }

        if (preg_match($pattern, $content, $matches)) {
            $content = str_replace($matches[0], $newContent, $content);
        } else {
            $content = str_replace($searchWord, $searchWord . PHP_EOL . $newContent, $content);
        }

        file_put_contents($filepath, $content);
    }

    protected static function appendRoutes(string $RouteType = "web")
    {
        self::appendFile("routes/" . $RouteType . '.php', 'routes.stub', 'use Illuminate\Support\Facades\Route;');
    }

    protected static function appendSASS()
    {
		$path = resource_path('sass/app.scss');
		if (!File::exists($path)) {
			$content = self::boilerplateString('@import "./boilerplate/boilerplate.scss";', "scss");
            File::put($path, $content);
        }
        self::appendFile("resources/sass/app.scss", 'scss.stub', '@import "./boilerplate/boilerplate.scss";');
    }

    protected static function appendJS()
    {
		$path = resource_path('js/app.js');
		if (!File::exists($path)) {
			$content = self::boilerplateString("import './boilerplate/boilerplate.js';", "js");
            File::put($path, $content);
        }
        self::appendFile("resources/js/app.js", 'js.stub', "import './bootstrap';");
    }

    protected static function appendExceptions()
    {
		self::appendFile("bootstrap/app.php", 'cookies.stub', '->withMiddleware(function (Middleware $middleware) {');
        self::appendFile("bootstrap/app.php", 'exceptions.stub', '->withExceptions(function (Exceptions $exceptions) {');
        self::appendFile("bootstrap/app.php", 'exceptionUses.stub', 'use Illuminate\Foundation\Application;');
        //remove old version exceptions
        File::deleteDirectory("../app/Exceptions");
    }
}
