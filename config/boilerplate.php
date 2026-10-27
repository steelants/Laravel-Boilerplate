<?php

use SteelAnts\LaravelBoilerplate\Models\Activity;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Models\Session;
use SteelAnts\LaravelBoilerplate\Models\Setting;
use SteelAnts\LaravelBoilerplate\Models\Subscription;

return [
    /*
    |--------------------------------------------------------------------------
    | System Admins
    |--------------------------------------------------------------------------
    |
    |
    */

    'system_admins'      => explode(',', env('APP_SYSTEM_ADMINS', '')),
    'system_admins_mail' => env('APP_SYSTEM_ADMINS_MAIL', '') ? explode(',', env('APP_SYSTEM_ADMINS_MAIL', '')) : '',
    'backup'             => [
        'enviroment'    => (bool) env('BACKUP_ENV', false),
        'database'      => (bool) env('BACKUP_DATABASE', true),
        'storage_paths' => explode(',', env('BACKUP_STORAGE_PATHS', 'app')), // Later storage_path() is used to get full path
        'storage'       => (bool) env('BACKUP_STORAGE', true),
        'restore'       => (bool) env('BACKUP_RESTORE', true), // false turns restoring from the UI and code off completely
    ],

    'models' => [
        'activity'     => Activity::class,
        'file'         => File::class,
        'setting'      => Setting::class,
        'session'      => Session::class,
        'subscription' => Subscription::class,
    ],

    'layouts' => [
        'default' => 'layout-app',
        'system'  => 'layout-app',
    ],

    'jobs' => [
        'namespaces' => [
            'App\\Jobs\\',
            'SteelAnts\\LaravelBoilerplate\\Jobs\\',
            'SteelAnts\\LaravelBoilerplate\\Dashboard\\Jobs\\',
        ],
    ],
];
