<?php

namespace App\Providers;

use App\Models\Tenant;
use App\Services\TenantManager;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(TenantManager::class, function () {
            $tenant = null;
            if (!app()->runningInConsole()) {
                $tenant = Tenant::where('domain', explode(".", request()->getHost())[0])
                    ->with(['users', 'settings'])
                    ->first();

                if (is_null($tenant)) {
                    abort(404, 'Tenant ' . explode(".", request()->getHost())[0] . ' not found (' . request()->getHost() . ')');
                    die();
                }
            }

            return new TenantManager($tenant);
        });
    }
}
