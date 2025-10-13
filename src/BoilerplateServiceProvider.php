<?php

namespace SteelAnts\LaravelBoilerplate;

use App\Http\Middleware\GenerateMenus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use SteelAnts\LaravelBoilerplate\Console\Commands\DispatchJob;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeCrudCommand;
use SteelAnts\LaravelBoilerplate\Facades\Alert;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;
use SteelAnts\LaravelBoilerplate\Listeners\UserEventSubscriber;
use SteelAnts\LaravelBoilerplate\Livewire\File\Gallery;
use SteelAnts\LaravelBoilerplate\Livewire\Settings\Form;

class BoilerplateServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'boilerplate');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'boilerplate');
        $this->loadMigrationsFrom(dirname(__DIR__) . '/database/migrations');

        Blade::componentNamespace('SteelAnts\LaravelBoilerplate\View\Components', 'boilerplate');

		if (class_exists('App\Http\Middleware\GenerateMenus')) {
			$router = $this->app['router'];
            $router->pushMiddlewareToGroup('web', GenerateMenus::class);
        }

		Event::subscribe(UserEventSubscriber::class);
		Livewire::component('boilerplate.file.gallery', Gallery::class);
		Livewire::component('boilerplate.settings.form', Form::class);

        if ($this->app->runningInConsole()) {
            $this->bootConsole();
        }
    }

	public function bootConsole()
    {
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new Backup())->dailyAT('00:00')->withoutOverlapping();
        });

		$this->commands([InstallCommand::class]);
        $this->commands([MakeCrudCommand::class]);

        $this->commands([MakeBasicTestsCommand::class]);
        $this->commands([DispatchJob::class]);

        $this->publishes([
            __DIR__ . '/../lang'            => $this->app->langPath('vendor/boilerplate'),
            __DIR__ . '/../resources/views' => resource_path('views/vendor/boilerplate'),
        ]);

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'boilerplate-migrations');

        $this->publishes([
            dirname(__DIR__) . '/config/boilerplate.php' => config_path('boilerplate.php'),
        ], 'boilerplate-config');

        $this->publishes([
            dirname(__DIR__) . '/resources/views' => resource_path('views/vendor/cms'),
        ], 'boilerplate-views');

        # schedule tasks from db https://stackoverflow.com/a/38664283
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->everyMinute();
        // });
	}

    public function register()
    {
        $this->app->alias('Menu', Menu::class);
        $this->app->alias('Alert', Alert::class);

		$this->mergeConfigFrom(
			__DIR__.'/../config/boilerplate.php',
			'boilerplate'
		);
    }
}
