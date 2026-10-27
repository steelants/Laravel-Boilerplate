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
    'restore'       => (bool) env('BACKUP_RESTORE', true),
],
```

| Option | Description |
|---|---|
| `database` | Include the database dump |
| `storage` | Include storage files |
| `storage_paths` | Storage paths to include (resolved using `storage_path()`) |
| `enviroment` | Include the `.env` file |
| `restore` | Allow restoring a backup. `false` hides the restore button, the restore modal returns 404 and `BackupService::restore()` throws, so nothing can overwrite the data from a backup |

With a published `config/boilerplate.php` add the `restore` key to its `backup` array to turn restoring
off - a missing key counts as `true`.

The job only ever writes the archives of the day it runs for - dropping old archives is left
to housekeeping.

### Upgrading an existing project

Nothing has to be added to `.env` or to the published config. One thing is worth doing anyway:
re-publish the backup prefabs (`App\Http\Controllers\System\BackupController`,
`App\Livewire\Backup\*` and `resources/views/system/backup/index.blade.php`) and drop the
`run` and `restore` routes from `routes/web.php`. Manual backups and restores now run from modals.
An older published controller keeps working, but both a failed backup and a failed restore
(`Restore::dispatchSync()`, now deprecated) end up as a 500 page instead of an error message.
The backup itself stays safe either way.

### Queue and cache requirements

The "Start Backup" modal queues the `Backup` job and polls its status from the cache:

- a queue worker has to run (with `QUEUE_CONNECTION=sync` the backup runs inside the request),
- the web server and the worker have to share one cache store (not `array`),
- the queue `retry_after` (`config/queue.php`, 90s by default) has to be longer than the job's
  600s timeout, otherwise a second worker picks the still running backup up again and reports
  it as failed.

Backup and restore share one cache lock, so only one of them runs at a time. A backup that finds
the lock taken (e.g. the scheduled one while a manual backup is running) logs a warning and quits.
The lock needs a cache store that supports atomic locks (`database`, `redis`, `file`, `memcached`).

When the system kills the process (OOM killer, `kill -9`, a server restart), nothing gets to clean up:

- a killed **backup** releases its lock in `failed()` once the worker picks the job up again after
  `retry_after`. Without a worker the lock expires after 15 minutes, and a status that stopped
  updating for that long is shown as failed. The previous archives are never touched - they are only
  replaced by an atomic rename of a finished archive.
- a killed **restore** leaves the data half restored and keeps the lock for up to an hour. A restore
  ignores `max_execution_time` and a closed browser tab, but not the PHP-FPM
  `request_terminate_timeout`, which should be raised (or disabled) for the backup page. To release the
  lock right away: `php artisan tinker --execute="SteelAnts\LaravelBoilerplate\Services\BackupService::restoreLock()->forceRelease();"`
A restore runs synchronously in the request, never on the queue - restoring `.env` or the
database could change the queue connection, `APP_KEY` or the `jobs` table under the worker.

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
