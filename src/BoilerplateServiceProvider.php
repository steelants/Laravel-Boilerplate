<?php

namespace SteelAnts\LaravelBoilerplate;

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

    }

    public function register()
    {
    }
}
