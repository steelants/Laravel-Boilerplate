<?php

namespace SteelAnts\LaravelBoilerplate;

use App\Jobs\Backup;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\DispatchJob;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeCrudCommand;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Support\MenuCollector;
use App\Http\Middleware\GenerateMenus;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'boilerplate');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'boilerplate');
        $this->loadMigrationsFrom(dirname(__DIR__) . '/database/migrations');

        if (class_exists('App\Http\Middleware\GenerateMenus')) {
            $router = $this->app['router'];
            $router->pushMiddlewareToGroup('web', GenerateMenus::class);
        }

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new Backup())->dailyAT('00:00')->withoutOverlapping();
        });

        $this->publishes([
            __DIR__ . '/../lang'                  => $this->app->langPath('vendor/boilerplate'),
            __DIR__ . '/../resources/views/views' => resource_path('views/vendor/boilerplate'),
        ]);

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'boilerplate-migrations');

        $this->publishes([
            dirname(__DIR__) . '/config/cms.php' => config_path('cms.php'),
        ], 'cms-config');

        $this->publishes([
            dirname(__DIR__) . '/resources/views' => resource_path('views/vendor/cms'),
        ], 'boilerplate-views');

        $this->commands([InstallCommand::class]);
        $this->commands([MakeCrudCommand::class]);

        $this->commands([MakeBasicTestsCommand::class]);
        $this->commands([DispatchJob::class]);


        # schedule tasks from db https://stackoverflow.com/a/38664283
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->everyMinute();
        // });
    }

    public function register()
    {
        $this->app->bind('menu', MenuCollector::class);
        $this->app->alias('Menu', Menu::class);
    }
}
