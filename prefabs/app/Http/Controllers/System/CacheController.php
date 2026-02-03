<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CacheController extends BaseController
{
    public function index()
    {
         /*Cache::put('test', 'test');
         Cache::remember('users', 200, function () {
             return DB::table('users')->get();
         });*/
        $cache_driver = config('cache.default');

        $cache_items = [];
        $storage = Cache::getStore(); // will return instance of FileStore
        if ($cache_driver == 'redis') {
            $redisConnection = $storage->connection();
            foreach ($redisConnection->command('keys', ['*']) as $full_key) {
                $cache_items[] = [
                    'key'       => str_replace($storage->getPrefix(), "", $full_key),
                    'expire_at' => null,
                ];
            }
        } elseif ($cache_driver == 'file') {
            $cachePath = $storage->getDirectory();
            $items = File::allFiles($cachePath);
            foreach ($items as $file2) {
                $cache_items[] = [
                    'key'       => $file2->getFilename(),
                    'expire_at' => Carbon::parse(substr($file2->getContents(), 0, 10))->setTimezone(config('app.timezone')),
                ];
            }
        } elseif ($cache_driver == 'database') {
            $items = DB::select("SELECT * FROM ".config('cache.stores.database.table').";");
            foreach ($items as $item) {
                $cache_items[] = [
                    'key'       => $item->key,
                    'expire_at' => Carbon::parse($item->expiration)->setTimezone(config('app.timezone')),
                ];
            }
        }

        //TODO: ADD SUPPORT FOR MEM CASH AND DB

        // OPcache status
        $opcache_data = [
			'loaded' => extension_loaded('opcache') || extension_loaded('Zend OPcache') || function_exists('opcache_get_status'),
            'enabled' => false,
            'memory_used' => 0,
            'memory_free' => 0,
            'validate_timestamps' => false,
            'revalidate_freq' => 0,
        ];

        if ($opcache_data['loaded']) {
            $opcache_status = opcache_get_status();
            $opcache_data['enabled'] = $opcache_status !== false;

            if ($opcache_data['enabled']) {
                $opcache_data['memory_used'] = round($opcache_status['memory_usage']['used_memory'] / 1024 / 1024, 2);
                $opcache_data['memory_free'] = round($opcache_status['memory_usage']['free_memory'] / 1024 / 1024, 2);
            }

            $opcache_data['validate_timestamps'] = (bool) ini_get('opcache.validate_timestamps');
            $opcache_data['revalidate_freq'] = (int) ini_get('opcache.revalidate_freq');
        }

        return view('system.cache.index', [
            'layout'       => config('boilerplate.layouts.system'),
            'cache_items'  => $cache_items,
            'cache_driver' => $cache_driver,
            'opcache'      => $opcache_data,
        ]);
    }

    public function clear()
    {
        Cache::flush();

        return redirect()->route('system.cache.index')->with('success', __('Cache logs'));
    }
}
