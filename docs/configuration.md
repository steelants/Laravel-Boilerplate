# Configuration

SteelAnts Laravel-Boilerplate is configured using the `config/boilerplate.php` file published by the installer.


## System Admins

System administrators are defined by comma separated user IDs:

```env
APP_SYSTEM_ADMINS=1,2
APP_SYSTEM_ADMINS_MAIL=admin@example.com
```

```php
'system_admins'      => explode(',', env('APP_SYSTEM_ADMINS', '')),
'system_admins_mail' => env('APP_SYSTEM_ADMINS_MAIL', '') ? explode(',', env('APP_SYSTEM_ADMINS_MAIL', '')) : '',
```

System admins can access the system management pages.


## Backups

Backup behavior is controlled using environment variables:

```php
'backup' => [
    'database'      => (bool) env('BACKUP_DATABASE', true),
    'storage'       => (bool) env('BACKUP_STORAGE', true),
    'storage_paths' => explode(',', env('BACKUP_STORAGE_PATHS', 'app')),
    'enviroment'    => (bool) env('BACKUP_ENV', true),
],
```

| Option | Description |
|---|---|
| `database` | Include the database dump |
| `storage` | Include storage files |
| `storage_paths` | Storage paths to include (resolved using `storage_path()`) |
| `enviroment` | Include the `.env` file |


## Models

Built-in models can be overridden by your own classes:

```php
'models' => [
    'activity'     => Activity::class,
    'file'         => File::class,
    'setting'      => Setting::class,
    'session'      => Session::class,
    'subscription' => Subscription::class,
],
```


## Layouts

Layout views used by the package:

```php
'layouts' => [
    'default' => 'layout-app',
    'system'  => 'layout-app',
],
```

The `system` layout is used by Livewire components with the `SystemPage` trait.


## Jobs

Namespaces scanned for dispatchable jobs on the job management page:

```php
'jobs' => [
    'namespaces' => [
        'App\\Jobs\\',
        'SteelAnts\\LaravelBoilerplate\\Jobs\\',
    ],
],
```


## Next Steps

Continue with:

- [Usage](usage.md)
- [Model Traits](traits.md)
- [Commands](commands.md)
