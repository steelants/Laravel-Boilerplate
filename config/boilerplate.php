<?php

use SteelAnts\LaravelBoilerplate\Models\Activity;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Models\Setting;
use SteelAnts\LaravelBoilerplate\Models\Session;
use SteelAnts\LaravelBoilerplate\Models\Subscription;

return [
	/*
    |--------------------------------------------------------------------------
    | System Admins
    |--------------------------------------------------------------------------
    |
    |
    */

	'system_admins' => explode(',', env('APP_SYSTEM_ADMINS', '')),
	'system_admins_mail' => explode(',', env('APP_SYSTEM_ADMINS_MAIL', '')),
	'backup' => [
		'database' 		=> (bool) env('BACKUP_DATABASE', true),
		'storage' 		=> (bool) env('BACKUP_STORAGE', true),
		'storage_paths' => explode(',', env('BACKUP_STORAGE_PATHS', 'app')), # Later storage_path() is used to get full path
		'enviroment' 	=> (bool) env('BACKUP_ENV', true),
	],

	'models' => [
		'activity' 	   => Activity::class,
		'file' 		   => File::class,
		'activity'	   => Activity::class,
		'setting'	   => Setting::class,
		'session' 	   => Session::class,
		'setting' 	   => Setting::class,
		'subscription' => Subscription::class,
	],

	'layouts' => [
		'default'    => 'layout-app',
		'system' 	 => 'layout-app',
	],
];
