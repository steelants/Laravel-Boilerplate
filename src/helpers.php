<?php

use Illuminate\Support\Facades\Cookie;

if(!function_exists('getToggleState')) {
    function getToggleState($name){
        $cookie = Cookie::get('toggleState');
        if($cookie == null) return '';

        $cookie = json_decode($cookie, true);
        return $cookie[$name] ?? '';
    }
}
