<?php

return [
	/*
    |--------------------------------------------------------------------------
    | System Admins
    |--------------------------------------------------------------------------
    |
    |
    */

	'system_admins' => explode(',', env('APP_SYSTEM_ADMINS', '')),
	'system_admins_mail' => explode(',', env('APP_SYSTEM_ADMINS', '')),
	'backups' => [
		'database' =>  (bool) env('BACKUP_DATABASE', true),
		'storage' =>  (bool) env('BACKUP_STORAGE', true),
		'enviroment' =>  (bool) env('BACKUP_ENV', true),
	],
];
