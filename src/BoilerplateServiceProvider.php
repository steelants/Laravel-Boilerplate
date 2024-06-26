<?php

namespace SteelAnts\LaravelBoilerplate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\DispatchJob;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeCrudCommand;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Support\MenuBuilder;
use SteelAnts\LaravelBoilerplate\Support\MenuCollector;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'boilerplate');

        if (class_exists('App\Http\Middleware\GenerateMenus')) {
            $router = $this->app['router'];
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\GenerateMenus::class);
        }

        if (!$this->app->runningInConsole()) {
            return;
        }

        if (class_exists('\App\Jobs\Backup')) {
            $this->app->booted(function () {
                $schedule = app(Schedule::class);
                $schedule->job(new \App\Jobs\Backup())->dailyAT('00:00')->withoutOverlapping();
            });
        }

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/boilerplate'),
            __DIR__ . '/../database/migrations' => $this->app->databasePath('migrations'),
        ]);

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
