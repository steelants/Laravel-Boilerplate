<?php

namespace SteelAnts\LaravelBoilerplate;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'boilerplate');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/boilerplate'),
        ]);

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([InstallCommand::class]);
        $this->commands([MakeBasicTestsCommand::class]);

        # schedule tasks from db https://stackoverflow.com/a/38664283
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->everyMinute();
        // });
    }

    public function register()
    {
    }
}
