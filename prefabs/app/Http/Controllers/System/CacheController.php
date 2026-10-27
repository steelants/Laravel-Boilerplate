<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use SteelAnts\LaravelBoilerplate\Services\CacheService;

class CacheController extends BaseController
{
    public function index()
    {
        return view('system.cache.index', [
            'layout'       => config('boilerplate.layouts.system'),
            'cache_driver' => config('cache.default'),
            'opcache'      => CacheService::opcache(),
            'cache_items'  => CacheService::items(),
        ]);
    }

    public function clear()
    {
        CacheService::flush();

        return redirect()->route('system.cache.index')->with('success', __('Cache logs'));
    }
}
