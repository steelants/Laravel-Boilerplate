<?php

namespace SteelAnts\LaravelBoilerplate\Tests;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as BaseTestCase;
use SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('setting_field.test.from.config.value', 'config-default-value');
    }

    /**
     * 'file.serv' se v reálné appce definuje v jejím vlastním routes/web.php
     * (viz prefabs/app/Http/Controllers/FileController.php) — balíček tuhle routu sám
     * neregistruje, takže si ji pro testy musíme nastavit stejně.
     */
    protected function defineRoutes($router): void
    {
        $router->get('/files/{path}/{file_name}/{public?}', function (string $path = '', string $file_name = '', bool $public = false) {
            $disk = $public ? 'public' : 'local';
            $key = trim(str_replace('-', DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . $file_name, DIRECTORY_SEPARATOR);

            return Storage::disk($disk)->exists($key) ? 'ok' : abort(404);
        })->where('path', '.*')->name('file.serv');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [BoilerplateServiceProvider::class];
    }
}
