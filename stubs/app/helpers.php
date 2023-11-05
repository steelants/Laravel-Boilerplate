<?php

use App\Models\Tenant;
use App\Services\TenantManager;

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
