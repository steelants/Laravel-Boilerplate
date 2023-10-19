<?php

namespace SteelAnts\LaravelBoilerplate;

use Illuminate\Support\ServiceProvider;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([InstallCommand::class]);
    }

    public function register()
    {
    }
}
