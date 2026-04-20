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

        $RouteFilePath = base_path('routes/web.php');

        if (strpos(file_get_contents($RouteFilePath), 'Route::auth();') === false) {
            if (strpos(file_get_contents('bootstrap/app.php'), "api: __DIR__.'/../routes/api.php',") === false) {
                $this->call('install:api', ['--without-migration-prompt' => true, '--force' => $this->option('force')]);
            }

            $this->call('install:auth', ['--force' => $this->option('force')]);
            file_put_contents($RouteFilePath, str_replace('Route::auth();', '', file_get_contents($RouteFilePath)));
        }

        $this->components->info('Installing Boilerplate Scaffolding');

        $hashFilePath = storage_path('boilerplate_install.json');
        $manifest = $this->loadManifest($hashFilePath);

        $clear = [];
        $prompted = [];

        $prefabTypes = $this->option('views-only')
            ? ['resources/views', 'resources/js/boilerplate', 'resources/sass/boilerplate', 'storage']
            : ['app', 'resources/views', 'resources/js/boilerplate', 'resources/sass/boilerplate', 'storage', 'config'];

        foreach ($prefabTypes as $type) {
            $this->collectPrefabs($type, $manifest, $clear, $prompted);
        }

        // Phase 1 — safe files (new or unmodified by user)
        foreach ($clear as $item) {
            self::checkDirectory(dirname($item['dest']));
            copy($item['stub'], $item['dest']);
            $manifest['files'][$item['key']] = $item['sourceHash'];
            $this->components->info("Installed: {$item['key']}");
        }

        // Save progress and pause before touching customized files
        if (!empty($prompted)) {
            $this->saveManifest($hashFilePath, $manifest);
            $this->components->warn('Safe files installed. Please commit your changes before continuing.');
            $this->newLine();

            // Phase 2 — customized / untracked files
            foreach ($prompted as $item) {
                $label = $item['wasCustomized'] ? 'customized' : 'untracked';
                if ($this->option('force') || $this->components->confirm("The [{$item['key']}] was {$label}. Replace it?")) {
                    self::checkDirectory(dirname($item['dest']));
                    copy($item['stub'], $item['dest']);
                    $manifest['files'][$item['key']] = $item['sourceHash'];
                    $this->components->info("Installed: {$item['key']}");
                }
            }
        }

        if (!$this->option('views-only')) {
            $this->components->info('Installing Boilerplate Scaffolding');

            self::updatePackagesJson();
            self::updateVite();
            self::removeNodeModules();

            $this->components->info('Adding Routes');
            self::appendRoutes();
            $this->cleanupLegacyBootstrap();

            if (!$this->option('no-migration')) {
                $this->components->warn('Running Migrations');
                Artisan::call('migrate');
            }
        }

        $this->saveManifest($hashFilePath, $manifest);

        $this->components->warn('Cleaning Cashes');
        Artisan::call('storage:link');
        Artisan::call('optimize:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $this->components->warn('Please run [npm install && npm run build] to compile your fresh scaffolding.');
    }

    private function loadManifest(string $path): array
    {
        if (!File::exists($path)) {
            return ['_meta' => [], 'files' => []];
        }

        $data = json_decode(File::get($path), true) ?? [];

        // Migrate old flat format (keys without _meta / files wrapper)
        if (!isset($data['files'])) {
            $files = array_filter($data, fn ($k) => !str_starts_with($k, '_'), ARRAY_FILTER_USE_KEY);
            $data = ['_meta' => [], 'files' => $files];
        }

        return $data;
    }

    private function saveManifest(string $path, array $manifest): void
    {
        $manifest['_meta']['updated_at'] = now()->toIso8601String();
        if (empty($manifest['_meta']['installed_at'])) {
            $manifest['_meta']['installed_at'] = now()->toIso8601String();
        }

        ksort($manifest['files']);

        File::put($path, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }

    protected function collectPrefabs(string $type, array $manifest, array &$clear, array &$prompted): void
    {
        $baseDir = __DIR__ . '/../../..';
        $moduleRootPath = str_replace(DIRECTORY_SEPARATOR, '/', realpath($baseDir . '/prefabs/' . $type));
        $laravelSubPath = '/' . $type;

        foreach (File::allFiles($moduleRootPath) as $file) {
            try {
                $relPath = str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname());
                $fileKey = ltrim($laravelSubPath . '/' . $relPath, '/');
                $stubFullPath = $file->getPathname();
                $destFullPath = base_path($laravelSubPath . '/' . $relPath);

                $sourceHash = hash_file('sha256', $stubFullPath);
                $storedHash = $manifest['files'][$fileKey] ?? null;
                $destHash = file_exists($destFullPath) ? hash_file('sha256', $destFullPath) : null;

                if ($sourceHash === $storedHash && $destHash === $storedHash) {
                    continue;
                }

                $wasCustomized = $destHash !== null && $storedHash !== null && $destHash !== $storedHash;
                $isUntracked = $destHash !== null && $storedHash === null;
                $item = compact('fileKey', 'stubFullPath', 'destFullPath', 'sourceHash', 'wasCustomized') + ['key' => $fileKey, 'stub' => $stubFullPath, 'dest' => $destFullPath];

                if ($wasCustomized || $isUntracked) {
                    $prompted[] = $item;
                } else {
                    $clear[] = $item;
                }
            } catch (Throwable $th) {
                $this->components->error($th->getMessage());
            }
        }
    }

    protected static function updatePackagesJson()
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        unset($packages['devDependencies']);
        $packages['dependencies']['jquery'] = '^3.6.1';
        $packages['dependencies']['sass'] = '1.77.6';
        $packages['dependencies']['bootstrap'] = '^5.3';
        $packages['dependencies']['@fortawesome/fontawesome-free'] = '^5.15.4';
        $packages['dependencies']['@popperjs/core'] = '^2.11.6';
        $packages['dependencies']['vite'] = '^6.2.0';
        $packages['dependencies']['laravel-vite-plugin'] = '^1.0.0';
        $packages['dependencies']['quill'] = '^2.0.3';
        $packages['dependencies']['quill-magic-url'] = '^4.2.0';
        $packages['dependencies']['quill-mention'] = '^6.0.2';
        $packages['dependencies']['quill-table-ui'] = '^1.0.7';
        $packages['devDependencies']['postcss'] = '^8.5.6';
        $packages['devDependencies']['postcss-hover-media-feature'] = '^1.0.2';

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

    protected static function checkDirectory($directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    protected function updateVite()
    {
        // TODO: Include rather than override
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

    protected static function appendRoutes(string $RouteType = 'web')
    {
        self::appendFile('routes/' . $RouteType . '.php', 'routes.stub', 'use Illuminate\Support\Facades\Route;');
    }

    protected static function appendSASS()
    {
        $importFileString = '@import "./boilerplate/boilerplate.scss";';
        $path = resource_path('sass/app.scss');
        if (!File::exists($path)) {
            self::checkDirectory(dirname($path));
            $content = self::boilerplateString($importFileString, 'scss');
            File::put($path, $content);
        }

        $ClassFileContent = file_get_contents($path);
        if (!str_contains($ClassFileContent, $importFileString)) {
            self::appendFile('resources/sass/app.scss', 'scss.stub', $importFileString);
        }
    }

    protected static function appendJS()
    {
        $importFileString = "import './boilerplate/boilerplate.js';";
        $path = resource_path('js/app.js');
        if (!File::exists($path)) {
            self::checkDirectory(dirname($path));
            $content = self::boilerplateString($importFileString, 'js');
            File::put($path, $content);
        }

        $ClassFileContent = file_get_contents($path);
        if (!str_contains($ClassFileContent, $importFileString)) {
            self::appendFile('resources/js/app.js', 'js.stub', $importFileString);
        }
    }

    protected function cleanupLegacyBootstrap(): void
    {
        $path = base_path('bootstrap/app.php');
        $content = File::get($path);
        $changed = false;

        foreach (['exceptions', 'exceptionUses', 'cookies'] as $name) {
            $pattern = '/\n?[ \t]*\/\* BOILERPLATE ' . $name . ' \*\/.*?\/\* BOILERPLATE ' . $name . ' \*\//s';
            $cleaned = preg_replace($pattern, '', $content);
            if ($cleaned !== $content) {
                $content = $cleaned;
                $changed = true;
            }
        }

        if ($changed) {
            File::put($path, $content);
            $this->components->warn('Removed legacy BOILERPLATE blocks from bootstrap/app.php');
        }

        File::deleteDirectory(base_path('app/Exceptions'));
    }
}
