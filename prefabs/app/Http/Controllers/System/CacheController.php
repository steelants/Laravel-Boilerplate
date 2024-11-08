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
                    'key' => str_replace($storage->getPrefix(), "", $full_key), 
                    'expire_at' => null
                ];
            }
        } elseif ($cache_driver == 'file') {
            $cachePath = $storage->getDirectory();
            $items = File::allFiles($cachePath);
            foreach ($items as $file2) {
                $cache_items[] = [
                    'key' => $file2->getFilename(), 
                    'expire_at' => Carbon::parse(substr($file2->getContents(), 0,10))->setTimezone(config('app.timezone'))
                ];
            }
        } elseif ($cache_driver == 'database') {
            $items = DB::select("SELECT * FROM ".config('cache.stores.database.table').";");
            foreach ($items as $item) {
                $cache_items[] = [
                    'key' => $item->key, 
                    'expire_at' => Carbon::parse($item->expiration)->setTimezone(config('app.timezone'))
                ];
            }
        }

        //TODO: ADD SUPPORT FOR MEM CASH AND DB

        return view('system.cache.index', [
            'cache_items' => $cache_items,
            'cache_driver' => $cache_driver,

        ]);
    }

    public function clear()
    {
        Cache::flush();

        return redirect()->route('system.cache.index')->with('success', __('boilerplate::ui.cache-cleared'));
    }
}
