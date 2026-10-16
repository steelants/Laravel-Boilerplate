<?php

use App\Http\Controllers\System\JobsController;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    // Web middleware (session, cookies) potřebuje klíč; sdílený TestCase ho nenastavuje.
    config()->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));

    // queue:retry čte failed_jobs z výchozího spojení z env, tady je to in-memory 'testing'.
    config()->set('queue.failed.database', 'testing');

    $prefabs = dirname(__DIR__, 3) . '/prefabs';

    // Prefaby žijí v namespace App\ a appka je dostane až po instalaci, proto je načítáme ručně.
    if (!class_exists('App\Http\Controllers\BaseController')) {
        class_alias(Illuminate\Routing\Controller::class, 'App\Http\Controllers\BaseController');
    }
    require_once $prefabs . '/app/Http/Controllers/System/JobsController.php';
    require_once $prefabs . '/app/View/Components/Alerts.php';

    // Prefab <x-alerts /> a jeho view components.alerts, stejně jako v nainstalované appce.
    view()->addLocation($prefabs . '/resources/views');
    Blade::component('alerts', App\View\Components\Alerts::class);

    Route::middleware('web')->group(function () {
        Route::get('/jobs', fn () => Blade::render('<x-alerts />'))->name('system.jobs.index');
        Route::get('/jobs/clear', [JobsController::class, 'clear']);
        Route::get('/jobs/rerun', [JobsController::class, 'rerun']);
        Route::get('/jobs/stop', [JobsController::class, 'stop']);
    });
});

describe('prefab JobsController flash messages', function () {
    it('shows the success flash of clear, rerun and stop in the rendered <x-alerts /> page', function (string $uri, string $message) {
        $this->followingRedirects()->get($uri)
            ->assertOk()
            ->assertSee($message)
            ->assertSee('snackbar');
    })->with([
        'clear' => ['/jobs/clear', 'Jobs cleared!'],
        'rerun' => ['/jobs/rerun', 'Failed jobs queued for retry!'],
        'stop'  => ['/jobs/stop', 'Waiting jobs stopped!'],
    ]);

    it('does not show the flash message again on the next request', function () {
        $this->followingRedirects()->get('/jobs/clear')->assertSee('Jobs cleared!');

        $this->get('/jobs')->assertDontSee('Jobs cleared!');
    });
});
