<?php

use Illuminate\Support\Facades\Route;
use SteelAnts\LaravelAuth\Routing\AuthRoutesMixin;

/**
 * Načte routes.stub do routeru stejně, jako to udělá appka po instalaci prefabů.
 * Kontrolery z App\ neexistují, což nevadí - router je resolvuje až při dispatchi.
 */
function loadRoutesStub(): void
{
    $code = file_get_contents(dirname(__DIR__, 3) . '/stubs/routes.stub');

    // Route::auth() dodává steelants/laravel-auth, ale jen když appka už má AuthController.
    if (!Route::hasMacro('auth')) {
        Route::mixin(new AuthRoutesMixin());
    }

    Route::middleware('web')->group(function () use ($code) {
        eval($code);
    });

    app('router')->getRoutes()->refreshNameLookups();
}

/**
 * @return array<string, string[]> název routy => soubory, které ji volají
 */
function routeNamesUsedInPrefabs(): array
{
    $root = dirname(__DIR__, 3) . '/prefabs';
    $used = [];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if (!preg_match('/\.(blade\.)?php$/', $file->getFilename())) {
            continue;
        }

        preg_match_all('/\broute\(\s*[\'"]([\w.\-]+)[\'"]/', file_get_contents($file->getPathname()), $matches);
        foreach ($matches[1] as $name) {
            $used[$name][] = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
        }
    }

    return $used;
}

/**
 * Routy, které stub záměrně nedefinuje - mimo rozsah 3.0.1, hlášeno v changelogu.
 * - totp.verify, verification.resend: opt-in v Route::auth(['totp' => true, 'verify' => true]).
 * - home.test-alert*: ukázkové routy z prefab home.blade.php.
 */
const PREFAB_ROUTES_OUTSIDE_STUB = [
    'totp.verify',
    'verification.resend',
    'home.test-alert',
    'home.test-alert-inline',
];

describe('routes.stub', function () {
    it('defines every route that prefab views and controllers call via route()', function () {
        loadRoutesStub();

        $used = routeNamesUsedInPrefabs();
        // Pojistka proti prázdnému zelenému testu (dnes je to přes 40 volání).
        expect(count($used))->toBeGreaterThan(30);

        $missing = [];
        foreach ($used as $name => $files) {
            if (!in_array($name, PREFAB_ROUTES_OUTSIDE_STUB, true) && !Route::has($name)) {
                $missing[] = $name . ' (' . implode(', ', array_unique($files)) . ')';
            }
        }

        expect($missing)->toBe([]);
    });

    it('registers the job rerun and stop routes', function () {
        loadRoutesStub();

        expect(Route::has('system.jobs.rerun'))->toBeTrue();
        expect(Route::has('system.jobs.stop'))->toBeTrue();
        expect(route('system.jobs.rerun', [], false))->toBe('/system/jobs/rerun');
        expect(route('system.jobs.stop', [], false))->toBe('/system/jobs/stop');
    });
});
