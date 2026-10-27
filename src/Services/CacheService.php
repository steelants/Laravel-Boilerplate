<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CacheService
{
    /**
     * List of ['key' => string, 'expire_at' => ?Carbon] for the default cache store.
     */
    public static function items(): array
    {
        // TODO: ADD SUPPORT FOR MEMCACHED
        return match (config('cache.default')) {
            'redis'    => self::redisItems(),
            'file'     => self::fileItems(),
            'database' => self::databaseItems(),
            default    => [],
        };
    }

    public static function flush(): void
    {
        Cache::flush();
    }

    /**
     * OPcache status, memory in MB.
     */
    public static function opcache(): array
    {
        $opcache = [
            'loaded'              => extension_loaded('opcache') || extension_loaded('Zend OPcache') || function_exists('opcache_get_status'),
            'enabled'             => false,
            'memory_used'         => 0,
            'memory_free'         => 0,
            'validate_timestamps' => false,
            'revalidate_freq'     => 0,
        ];

        if (!$opcache['loaded']) {
            return $opcache;
        }

        $status = opcache_get_status();
        $opcache['enabled'] = $status !== false;

        if ($opcache['enabled']) {
            $opcache['memory_used'] = round($status['memory_usage']['used_memory'] / 1024 / 1024, 2);
            $opcache['memory_free'] = round($status['memory_usage']['free_memory'] / 1024 / 1024, 2);
        }

        $opcache['validate_timestamps'] = (bool) ini_get('opcache.validate_timestamps');
        $opcache['revalidate_freq'] = (int) ini_get('opcache.revalidate_freq');

        return $opcache;
    }

    private static function redisItems(): array
    {
        $items = [];
        foreach (Cache::getStore()->connection()->command('keys', ['*']) as $full_key) {
            $items[] = [
                'key'       => str_replace(Cache::getStore()->getPrefix(), '', $full_key),
                'expire_at' => null,
            ];
        }

        return $items;
    }

    private static function fileItems(): array
    {
        $items = [];
        foreach (File::allFiles(Cache::getStore()->getDirectory()) as $file) {
            // FileStore prefixes the payload with a 10-digit unix timestamp of the expiration.
            $items[] = [
                'key'       => $file->getFilename(),
                'expire_at' => self::expiration(substr($file->getContents(), 0, 10)),
            ];
        }

        return $items;
    }

    private static function databaseItems(): array
    {
        $items = [];
        foreach (DB::table(config('cache.stores.database.table'))->get(['key', 'expiration']) as $item) {
            $items[] = [
                'key'       => $item->key,
                'expire_at' => self::expiration($item->expiration),
            ];
        }

        return $items;
    }

    private static function expiration(int|string $timestamp): Carbon
    {
        return Carbon::createFromTimestamp((int) $timestamp)->setTimezone(config('app.timezone'));
    }
}
