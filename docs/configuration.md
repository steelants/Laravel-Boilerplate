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
    'database'       => (bool) env('BACKUP_DATABASE', true),
    'storage'        => (bool) env('BACKUP_STORAGE', true),
    'storage_paths'  => explode(',', env('BACKUP_STORAGE_PATHS', 'app')),
    'enviroment'     => (bool) env('BACKUP_ENV', true),
    'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 0),
],
```

| Option | Description |
|---|---|
| `database` | Include the database dump |
| `storage` | Include storage files |
| `storage_paths` | Storage paths to include (resolved using `storage_path()`) |
| `enviroment` | Include the `.env` file |
| `retention_days` | How many days of backups are kept in `storage/backups`, `0` (default) keeps everything |

Pruning runs only after a successful backup and deletes every archive older than the window.
It is off by default because it deletes archives irreversibly - set `BACKUP_RETENTION_DAYS=3`
once you are sure the older archives in `storage/backups` are not needed.

### Upgrading an existing project

Nothing has to be added to `.env` or to the published config - the fail safe backup works as
it is, and retention stays off. Two things are worth doing anyway:

- `retention_days` only reaches an app whose published `config/boilerplate.php` contains the
  key, because `mergeConfigFrom()` merges the top level only: a published `'backup' => [...]`
  array replaces the package defaults wholesale. To use retention, add
  `'retention_days' => (int) env('BACKUP_RETENTION_DAYS', 0),` to that array and then set
  `BACKUP_RETENTION_DAYS` - the env variable alone does nothing.
- Re-publish `App\Http\Controllers\System\BackupController` (or copy its `run()` method). The
  job now throws when a backup fails, so an older controller turns a failed manual run into a
  500 page instead of an error message. The backup itself stays safe either way.

Archives are written to `storage/backups` as `Y-m-d_database.zip` and `Y-m-d_storage.zip`
(the `.env` file is stored inside the storage archive).

The backup job is fail safe: every archive is first built as a `.zip.part` file, verified with
`zip -T`, and only then renamed over the previous archive. If any step fails - a missing
`mysqldump`, wrong credentials, a full disk, the 600s timeout - the previous backup is left
untouched, no old archives are pruned, the admins get a failure mail and the job lands in
`failed_jobs` instead of reporting success. The job needs the `rm`, `cp`, `zip` and (depending
on the driver) `mysqldump` / `pg_dump` binaries to be available.


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
