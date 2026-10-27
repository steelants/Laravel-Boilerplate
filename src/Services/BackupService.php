<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Helpers\SizeHelper;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;
use SteelAnts\LaravelBoilerplate\Types\BackupStatusType;
use RuntimeException;
use Throwable;

class BackupService
{
    /**
     * Queues a backup unless one is already queued or running. Returns false when it was not queued.
     */
    public static function dispatch(): bool
    {
        if (self::isInProgress()) {
            return false;
        }

        self::setStatus(BackupStatusType::QUEUED);
        Backup::dispatch();

        return true;
    }

    /**
     * ['state' => BackupStatusType::*, 'message' => ?string, 'updated_at' => timestamp] or null when no backup ran yet.
     */
    public static function status(): ?array
    {
        return Cache::get('boilerplate.backup.status');
    }

    public static function setStatus(string $state, ?string $message = null): void
    {
        // Kept a bit longer than the job timeout, so a crashed worker cannot block new backups forever.
        Cache::put('boilerplate.backup.status', [
            'state'      => $state,
            'message'    => $message,
            'updated_at' => now()->timestamp,
        ], now()->addHour());
    }

    public static function isInProgress(): bool
    {
        return BackupStatusType::isInProgress(self::status()['state'] ?? null);
    }

    /**
     * Backups grouped by date: ['Y-m-d' => ['fileName' => [...], 'fileSize' => '1.2 MB']]
     */
    public static function list(): array
    {
        $backups = [];
        $path = storage_path('backups');

        if (!file_exists($path)) {
            return $backups;
        }

        foreach (File::allFiles($path . '/') as $file) {
            if (!Str::endsWith($file->getFilename(), '.zip')) {
                continue;
            }
            $date = explode('_', str_replace('.zip', '', $file->getFilename()))[0];
            $backups[$date]['fileSize'] = ($backups[$date]['fileSize'] ?? 0) + $file->getSize();
            $backups[$date]['fileName'][] = $file->getFilename();
        }

        foreach ($backups as $key => $backup) {
            $backups[$key]['fileSize'] = SizeHelper::getHumanReadableSize($backup['fileSize']);
        }

        return $backups;
    }

    public static function delete(string $backup_date): void
    {
        File::delete([
            storage_path('backups/' . $backup_date . '_database.zip'),
            storage_path('backups/' . $backup_date . '_storage.zip'),
        ]);
    }

    /**
     * Returns null when the date is not a valid backup date.
     */
    public static function parseDate(string $backup_date): ?Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $backup_date)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns false when nothing was selected; throws when a selected archive is missing or a step fails.
     *
     * Deliberately not a queued job: restoring .env or the database can change the queue connection,
     * APP_KEY or the jobs table under a running worker and leave it stuck.
     */
    public static function restore(string $backup_date, bool $database, bool $storage, bool $env): bool
    {
        if (!self::parseDate($backup_date)) {
            return false;
        }

        $env = $env && config('boilerplate.backup.enviroment');

        if (!$database && !$storage && !$env) {
            return false;
        }

        try {
            self::restoreArchives($backup_date, $database, $storage, $env);
        } catch (Throwable $e) {
            SystemAdminNotificationService::send(__('Restore Failed'), __('Restore failed') . ': ' . $e->getMessage());

            throw $e;
        }

        SystemAdminNotificationService::send(__('Restore Run successfully'), __('Restore Run successfully'));

        return true;
    }

    protected static function restoreArchives(string $backup_date, bool $database, bool $storage, bool $env): void
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
                self::execShellCommand($command, 'Clean Old Temp ' . $restorePath);
                Log::Info('Clean Old Temp ' . $restorePath);
            }
        }

        try {
            $dateSlug = $backup_date;

            // DATABASE RESTORE
            if ($database) {
                $dbZip = storage_path('backups/' . $dateSlug . '_database.zip');
                if (File::exists($dbZip)) {
                    $command = 'unzip -o ' . $dbZip . ' -d ' . $db_restore_path . ' 2>&1';
                    self::execShellCommand($command, 'Unzip of database backup ' . $dbZip);
                    Log::info('Unzipped database backup ' . $dbZip);

                    if (config('database.default') == 'sqlite') {
                        $sqliteFiles = glob($db_restore_path . '/*.sqlite');
                        if (!empty($sqliteFiles)) {
                            $sqliteFile = $sqliteFiles[0];
                            $command = 'cp ' . $sqliteFile . ' ' . database_path('database.sqlite') . ' 2>&1';
                            self::execShellCommand($command, 'Restore sqlite db from ' . $sqliteFile);
                            Log::info('Restore sqlite db from ' . $sqliteFile);
                        } else {
                            throw new RuntimeException('No .sqlite file found in the database backup');
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
                                $command = "psql -v ON_ERROR_STOP=1 -h $dbHost -U $dbUserName -d $dbName -f \"$sqlFile\" 2>&1";
                                self::execShellCommand($command, 'Restore ' . $dbName . ' db ' . $type);
                                Log::info('Restore ' . $dbName . ' db ' . $type);
                            } else {
                                throw new RuntimeException('No ' . $type . ' SQL file found in the database backup');
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
                                self::execShellCommand($command, 'Restore ' . $dbName . ' db ' . $type);
                                Log::info('Restore ' . $dbName . ' db ' . $type);
                            } else {
                                throw new RuntimeException('No ' . $type . ' SQL file found in the database backup');
                            }
                        }
                    }
                } else {
                    throw new RuntimeException('Database backup not found: ' . basename($dbZip));
                }
            }

            // STORAGE + .ENV RESTORE (share the same unzip)
            $needsStorageZip = $storage || $env;
            if ($needsStorageZip) {
                $fsZip = storage_path('backups/' . $dateSlug . '_storage.zip');
                if (File::exists($fsZip)) {
                    $command = 'unzip -o ' . $fsZip . ' -d ' . $fs_restore_path . ' 2>&1';
                    self::execShellCommand($command, 'Unzip of storage backup ' . $fsZip);
                    Log::info('Unzipped storage backup ' . $fsZip);

                    if ($storage) {
                        foreach (config('boilerplate.backup.storage_paths') as $storage_path) {
                            $src = $fs_restore_path . '/' . $storage_path;
                            if (File::exists($src)) {
                                $dst = storage_path($storage_path);
                                if (!File::exists($dst)) {
                                    File::makeDirectory($dst, 0755, true);
                                }
                                $command = 'cp -R ' . $src . '/. ' . $dst . ' 2>&1';
                                self::execShellCommand($command, 'Restore storage ' . $storage_path);
                                Log::info('Restore storage ' . $storage_path);
                            }
                        }
                    }

                    if ($env) {
                        $envBackup = $fs_restore_path . '/env.backup';
                        if (File::exists($envBackup)) {
                            $command = 'cp ' . $envBackup . ' ' . app()->environmentFilePath() . ' 2>&1';
                            self::execShellCommand($command, 'Restore .env');
                            Log::info('Restore .env');
                        } else {
                            throw new RuntimeException('env.backup not found in the storage backup');
                        }
                    }
                } else {
                    throw new RuntimeException('Storage backup not found: ' . basename($fsZip));
                }
            }
        } finally {
            // Leftover extracted dumps would keep a copy of the data (and .env) lying around.
            foreach ([$db_restore_path, $fs_restore_path] as $restorePath) {
                File::deleteDirectory($restorePath);
                Log::info('Cleanup ' . $restorePath);
            }
        }
    }

    /**
     * @return array<int, string> lines the command wrote to stdout/stderr
     */
    public static function execShellCommand(string $command, string $description): array
    {
        $output = [];
        $resultCode = 0;

        exec($command, $output, $resultCode);
        Log::debug($output);

        if ($resultCode !== 0) {
            // The command itself is never part of the message, it can carry database credentials.
            throw new RuntimeException($description . ' failed (exit code ' . $resultCode . '): ' . implode(' ', $output));
        }

        return $output;
    }
}
