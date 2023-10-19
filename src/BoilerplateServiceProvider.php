<?php

namespace SteelAnts\LaravelBoilerplate;

use Illuminate\Support\ServiceProvider;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;

class ModalServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //

        if (!$this->app->runningInConsole()){
            continue;
        }

        $this->commands([InstallCommand::class]);
    }

    public function register()
    {

    }
}