<?php

namespace SteelAnts\LaravelBoilerplate\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SteelAnts\LaravelBoilerplate\Attributes\AllowManualRun;

#[AllowManualRun()]
class Restore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Carbon $backup_date,
        public bool $restoreDatabase = true,
        public bool $restoreStorage = true,
        public bool $restoreEnv = false,
    ) {}

    public function handle()
    {
        // PREPARATION
        ini_set('date.timezone', 'Europe/Prague');
        $db_restore_path = storage_path('backups/tmp/restore_db');
        $fs_restore_path = storage_path('backups/tmp/restore_storage');

        foreach ([$db_restore_path, $fs_restore_path] as $restorePath) {
            if (!File::exists($restorePath)) {
                File::makeDirectory($restorePath, 0755, true);
            } else {
                $command = 'rm -r -f ' . $restorePath . '/*';
                exec($command, $output);
                Log::Info('Clean Old Temp ' . $restorePath);
                Log::Debug($output);
            }
        }

        $dateSlug = $this->backup_date->format('Y-m-d');

        // DATABASE RESTORE
        if ($this->restoreDatabase) {
            $dbZip = storage_path('backups/' . $dateSlug . '_database.zip');
            if (File::exists($dbZip)) {
                $command = 'unzip -o ' . $dbZip . ' -d ' . $db_restore_path . ' 2>&1';
                exec($command, $output);
                Log::info('Unzipped database backup ' . $dbZip);
                Log::Debug($output);

                if (config('database.default') == 'sqlite') {
                    $sqliteFiles = glob($db_restore_path . '/*.sqlite');
                    if (!empty($sqliteFiles)) {
                        $sqliteFile = $sqliteFiles[0];
                        $command = 'cp ' . $sqliteFile . ' ' . database_path('database.sqlite') . ' 2>&1';
                        exec($command, $output);
                        Log::info('Restore sqlite db from ' . $sqliteFile);
                        Log::Debug($output);
                    } else {
                        Log::warning('No .sqlite file found in ' . $db_restore_path);
                    }
                } elseif (config('database.default') == 'pgsql') {
                    $dbHost = config('database.connections.pgsql.host');
                    $dbName = config('database.connections.pgsql.database');
                    $dbUserName = config('database.connections.pgsql.username');
                    $dbPassword = config('database.connections.pgsql.password');

                    putenv('PGPASSWORD=' . $dbPassword);

                    foreach (['scheme', 'data'] as $type) {
                        $files = glob($db_restore_path . '/*_' . $type . '_*.sql');
                        if (!empty($files)) {
                            $sqlFile = $files[0];
                            $command = "psql -h $dbHost -U $dbUserName -d $dbName -f \"$sqlFile\" 2>&1";
                            exec($command, $output);
                            Log::info('Restore ' . $dbName . ' db ' . $type);
                            Log::Debug($output);
                        } else {
                            Log::warning('No ' . $type . ' SQL file found in ' . $db_restore_path);
                        }
                    }
                } else {
                    $dbHost = config('database.connections.mysql.host');
                    $dbName = config('database.connections.mysql.database');
                    $dbUserName = config('database.connections.mysql.username');
                    $dbPassword = config('database.connections.mysql.password');

                    foreach (['scheme', 'data'] as $type) {
                        $files = glob($db_restore_path . '/*_' . $type . '_*.sql');
                        if (!empty($files)) {
                            $sqlFile = $files[0];
                            $command = 'mysql --skip-ssl -h ' . $dbHost . ' -u ' . $dbUserName . ' -p' . $dbPassword . ' ' . $dbName . " < \"$sqlFile\" 2>&1";
                            exec($command, $output);
                            Log::info('Restore ' . $dbName . ' db ' . $type);
                            Log::Debug($output);
                        } else {
                            Log::warning('No ' . $type . ' SQL file found in ' . $db_restore_path);
                        }
                    }
                }
            } else {
                Log::warning('Database backup not found: ' . $dbZip);
            }
        }

        // STORAGE + .ENV RESTORE (share the same unzip)
        $needsStorageZip = $this->restoreStorage || $this->restoreEnv;
        if ($needsStorageZip) {
            $fsZip = storage_path('backups/' . $dateSlug . '_storage.zip');
            if (File::exists($fsZip)) {
                $command = 'unzip -o ' . $fsZip . ' -d ' . $fs_restore_path . ' 2>&1';
                exec($command, $output);
                Log::info('Unzipped storage backup ' . $fsZip);
                Log::Debug($output);

                if ($this->restoreStorage) {
                    foreach (config('boilerplate.backup.storage_paths') as $storage_path) {
                        $src = $fs_restore_path . '/' . $storage_path;
                        if (File::exists($src)) {
                            $dst = storage_path($storage_path);
                            if (!File::exists($dst)) {
                                File::makeDirectory($dst, 0755, true);
                            }
                            $command = 'cp -R ' . $src . '/. ' . $dst . ' 2>&1';
                            exec($command, $output);
                            Log::info('Restore storage ' . $storage_path);
                            Log::Debug($output);
                        }
                    }
                }

                if ($this->restoreEnv) {
                    $envBackup = $fs_restore_path . '/env.backup';
                    if (File::exists($envBackup)) {
                        $command = 'cp ' . $envBackup . ' ' . app()->environmentFilePath() . ' 2>&1';
                        exec($command, $output);
                        Log::info('Restore .env');
                        Log::Debug($output);
                    } else {
                        Log::warning('env.backup not found in storage zip');
                    }
                }
            } else {
                Log::warning('Storage backup not found: ' . $fsZip);
            }
        }

        // CLEANUP
        foreach ([$db_restore_path, $fs_restore_path] as $restorePath) {
            $command = 'rm -r -f ' . $restorePath;
            exec($command, $output);
            Log::Info('Cleanup ' . $restorePath);
            Log::Debug($output);
        }

        // MAIL NOTIFICATION
        if (!empty(config('boilerplate.system_admins_mail')) && config('boilerplate.system_admins_mail') != '') {
            Mail::raw(__('Restore Run successfully'), function ($message) {
                $message->to(config('boilerplate.system_admins_mail'))->subject(__('Restore Run successfully ') . config('app.name'));
            });
            Log::info('Sending Restore Notification');
        }
    }
}
