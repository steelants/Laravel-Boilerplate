<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CacheController extends Controller
{
    public function index()
    {
        // Cache::put('test', 'test');
        // Cache::remember('users', 200, function () {
        //     return DB::table('users')->get();
        // });
        $cache_driver = config('cache.default');

        $cache_items = [];
        $storage = Cache::getStore(); // will return instance of FileStore
        if ($cache_driver == 'redis') {
            $redisConnection = $storage->connection();
            foreach ($redisConnection->command('keys', ['*']) as $full_key) {
                $cache_items[] = str_replace($storage->getPrefix(), "", $full_key);
            }
        } else {
            $cachePath = $storage->getDirectory();
            $items = File::allFiles($cachePath);
            foreach ($items as $file2) {
                $cache_items[] = $file2->getFilename();
            }
        }
        
        //TODO: ADD SUPPORT FOR MEM CASH AND DB

        return view('system.cache.index', [
            'cache_items' => $cache_items,
            'cache_driver' => $cache_driver,

        ]);
    }

    
    public function clear(){
        Cache::flush();

        return redirect()->route('system.cache.index')->with('success',  __('boilerplate::ui.cache-cleared'));
    }
}
