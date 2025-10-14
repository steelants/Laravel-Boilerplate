<?php

use Illuminate\Support\Facades\Cookie;
use SteelAnts\LaravelBoilerplate\Models\Setting;

if (!function_exists('getToggleState')) {
    function getToggleState($name)
    {
        $cookie = Cookie::get('toggleState');
        if ($cookie == null) {
            return '';
        }

        $cookie = json_decode($cookie, true);
        return $cookie[$name] ?? '';
    }
}

if (! function_exists('settings')) {
    function settings($key, $default = null) {
        $value = Setting::whereNull('settable_id')->whereNull('settable_type')->where('index', $key)->get();
        if (empty($value)){
            return $default;
        }

        if (count($value) == 1){
            return $value->first()->value;
        }

        return $value->pluck('value', 'index')->toArray();
    }
}
