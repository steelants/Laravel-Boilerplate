<?php

namespace SteelAnts\LaravelBoilerplate;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;

use App\Models\Tenant;
use App\Services\TenantManager;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'boilerplate');




        if (!function_exists('tenantManager')) {
            /** @return TenantManager */
            function tenantManager()
            {
                return app(TenantManager::class);
            }
        }

        if (!function_exists('tenant')) {
            /** @return Tenant */
            function tenant()
            {
                return app(TenantManager::class)->getTenant();
            }
        }

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/boilerplate'),
            __DIR__ . '/../database/migrations' => $this->app->databasePath('migrations'),
        ]);

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
