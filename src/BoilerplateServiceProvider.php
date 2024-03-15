<?php

namespace SteelAnts\LaravelBoilerplate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\DispatchJob;

use App\Jobs\Backup;
use App\Models\Tenant;
use App\Services\TenantManager;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeCrudCommand;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'boilerplate');

        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new Backup)->dailyAT('00:00')->withoutOverlapping();;
        });

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
    }
}
