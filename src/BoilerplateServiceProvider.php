<?php

namespace SteelAnts\LaravelBoilerplate;

use App\Http\Middleware\GenerateMenus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use SteelAnts\LaravelBoilerplate\Console\Commands\DispatchJob;
use SteelAnts\LaravelBoilerplate\Console\Commands\InstallCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeBasicTestsCommand;
use SteelAnts\LaravelBoilerplate\Console\Commands\MakeCrudCommand;
use SteelAnts\LaravelBoilerplate\Facades\Alert;
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Support\AlertCollector;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;
use SteelAnts\LaravelBoilerplate\Listeners\UserEventSubscriber;
use SteelAnts\LaravelBoilerplate\Livewire\File\Gallery;
use SteelAnts\LaravelBoilerplate\Livewire\Hooks\AlertDispatcherHook;
use SteelAnts\LaravelBoilerplate\Livewire\Setting\Form;
use SteelAnts\LaravelBoilerplate\Traits\Auditable;
use SteelAnts\LaravelBoilerplate\Traits\AuditableDetailed;
use SteelAnts\LaravelBoilerplate\Traits\SupportSystemAdmins;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        if (class_exists('App\Http\Middleware\IsSystemAdmin')) {
            Route::aliasMiddleware('is-system-admin', \App\Http\Middleware\IsSystemAdmin::class);
        }

        EncryptCookies::except(['theme', 'layout-nav', 'toggleState', 'tabState']);

        /** @var \Illuminate\Foundation\Exceptions\Handler $handler */
        $handler = $this->app->make(ExceptionHandler::class);
        $handler->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->is('local/*')) {
                return response()->json(['message' => __('Record not found.')], 404);
            }
        });

        $userClass = config('auth.providers.users.model');
        if ($userClass && $this->classHasAnyTrait($userClass, [Auditable::class, AuditableDetailed::class])) {
            Event::subscribe(UserEventSubscriber::class);
        }

        Livewire::componentHook(AlertDispatcherHook::class);

        Livewire::component('boilerplate.file.gallery', Gallery::class);
        Livewire::component('boilerplate.setting.form', Form::class);

        Gate::define('is-system-admin', function ($user) {
            if ($this->classHasAnyTrait(get_class($user), [SupportSystemAdmins::class])) {
                return (bool) $user->isSystemAdmin;
            }

            return in_array($user->id, config('boilerplate.system_admins', []));
        });

        if ($this->app->runningInConsole()) {
            $this->bootConsole();
        }
    }

    private function classHasAnyTrait(string $class, array $traits): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        $usedTraits = class_uses_recursive($class);

        foreach ($traits as $trait) {
            if (isset($usedTraits[$trait])) {
                return true;
            }
        }

        return false;
    }

    public function bootConsole()
    {
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->job(new Backup)->dailyAT('00:00')->withoutOverlapping();
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

        // schedule tasks from db https://stackoverflow.com/a/38664283
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->everyMinute();
        // });
    }

    public function register()
    {
        $this->app->singleton(AlertCollector::class);
        $this->app->alias('Menu', Menu::class);
        $this->app->alias('Alert', Alert::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/boilerplate.php',
            'boilerplate'
        );
    }
}
