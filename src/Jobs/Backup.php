<?php

namespace SteelAnts\LaravelBoilerplate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use SteelAnts\LaravelBoilerplate\Attributes\AllowManualRun;
use Throwable;

#[AllowManualRun()]
#[Timeout(600)]
#[Tries(1)]
class Backup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle()
    {
        // PREPARATION
        ini_set('date.timezone', 'Europe/Prague');
        $date = date('Y-m-d', time());
        $fs_backup_path = storage_path('backups/tmp/storage');
        $db_backup_path = storage_path('backups/tmp/db');

        $backupDatabase = (bool) config('boilerplate.backup.database');
        $backupStorage = (bool) config('boilerplate.backup.storage');
        $backupEnvironment = (bool) config('boilerplate.backup.enviroment');

        // A component that is turned off is skipped completely, so its existing
        // archive is left alone instead of being replaced by an empty one.
        $archives = [];
        if ($backupDatabase) {
            $archives['database'] = $db_backup_path;
        }
        if ($backupStorage || $backupEnvironment) {
            // The .env file travels inside the storage archive, same as Restore expects it.
            $archives['storage'] = $fs_backup_path;
        }

        if (empty($archives)) {
            Log::debug('Backup skipped, every component is disabled');

            return;
        }

        $partFiles = [];

        try {
            foreach ([$db_backup_path, $fs_backup_path] as $backupPath) {
                $this->prepareTmpDirectory($backupPath);
            }

            if ($backupDatabase) {
                $this->backupDatabase($db_backup_path, $date);
            }

            if ($backupStorage) {
                $this->backupStorage($fs_backup_path);
            }

            if ($backupEnvironment) {
                $this->backupEnvironment($fs_backup_path);
            }

            // Build every archive under a temporary name first, ...
            foreach ($archives as $name => $backupPath) {
                $zippedFilePath = storage_path('backups/' . $date . '_' . $name . '.zip');
                $partFiles[$zippedFilePath] = $this->createArchive($backupPath, $zippedFilePath);
            }

            // ... and replace the previous backup only once all of them are complete.
            foreach ($partFiles as $zippedFilePath => $partFile) {
                $this->replaceArchive($partFile, $zippedFilePath);
                unset($partFiles[$zippedFilePath]);
            }
        } catch (Throwable $e) {
            foreach ($partFiles as $partFile) {
                File::delete($partFile);
            }

            Log::error('Backup failed: ' . $e->getMessage());
            $this->notify(__('Backup Failed'), __('Backup failed') . ': ' . $e->getMessage());

            throw $e;
        }

        $this->notify(__('Backup Run successfully'), __('Backup Run successfully'));
    }

    protected function prepareTmpDirectory(string $backupPath): void
    {
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);

            return;
        }

        $this->execShellCommand('rm -r -f ' . $backupPath . '/*', 'Cleanup of ' . $backupPath);
        Log::info('Clean Old Temp ' . $backupPath);
    }

    protected function backupDatabase(string $db_backup_path, string $date): void
    {
        if (config('database.default') == 'sqlite') {
            $dbFile = database_path('database.sqlite');
            $dbName = basename($dbFile, '.php');
            $backupFile = $db_backup_path . '/' . $dbName . '_' . $date . '.sqlite';

            $this->execShellCommand("cp $dbFile $backupFile 2>&1", 'Backup of ' . $dbName);
            Log::info('Backup ' . $dbName . ' db ');

            return;
        }

        if (config('database.default') == 'pgsql') {
            $dbHost = config('database.connections.pgsql.host');
            $dbName = config('database.connections.pgsql.database');
            $dbUserName = config('database.connections.pgsql.username');
            $dbPassword = config('database.connections.pgsql.password');

            foreach (['data', 'scheme'] as $type) {
                $parameters = '--schema-only';
                if ($type == 'data') {
                    $parameters = '--data-only';
                }

                putenv('PGPASSWORD=' . $dbPassword);

                $backupFile = $db_backup_path . '/' . $dbName . '_' . $type . '_' . $date . '.sql';
                $command = "pg_dump --no-comments $parameters -h $dbHost -U $dbUserName -d $dbName -f \"$backupFile\" 2>&1";

                $this->execShellCommand($command, 'Backup of ' . $dbName . ' ' . $type);
                Log::info('Backup ' . $dbName . ' db ' . $type);
            }

            return;
        }

        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUserName = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');

        foreach (['data', 'scheme'] as $type) {
            $parameters = '--no-data';
            if ($type == 'data') {
                $parameters = '--no-create-info';
            }

            $backupFile = $db_backup_path . '/' . $dbName . '_' . $type . '_' . $date . '.sql';
            // MySQL 8 dumps tablespaces by default, which needs the PROCESS privilege the
            // application user usually does not have - without this the dump aborts.
            $command = 'mysqldump --skip-ssl --skip-comments --no-tablespaces ' . $parameters . ' -h ' . $dbHost . ' -u ' . $dbUserName . ' -p' . $dbPassword . ' ' . $dbName . " -r $backupFile 2>&1";

            $this->execShellCommand($command, 'Backup of ' . $dbName . ' ' . $type);
            Log::info('Backup ' . $dbName . ' db ' . $type);
        }
    }

    protected function backupStorage(string $fs_backup_path): void
    {
        foreach (config('boilerplate.backup.storage_paths') ?? [] as $storage_path) {
            $this->execShellCommand('cp -R ' . storage_path($storage_path) . ' ' . $fs_backup_path, 'Backup of storage/' . $storage_path);
            Log::info('storage backup done');
        }
    }

    protected function backupEnvironment(string $fs_backup_path): void
    {
        $envBackupFile = $fs_backup_path . '/env.backup';
        $envSourceFile = app()->environmentFilePath();

        $this->execShellCommand('cp ' . $envSourceFile . ' ' . $envBackupFile, 'Backup of .env');
        Log::info('Backup .env');
    }

    /**
     * Zips $backupPath next to its final destination and verifies the result.
     * Returns the path of the finished, still temporary archive.
     */
    protected function createArchive(string $backupPath, string $zippedFilePath): string
    {
        $partFilePath = $zippedFilePath . '.part';
        File::delete($partFilePath);

        // An empty source directory means an earlier step produced nothing - zip would
        // just report "Nothing to do" and leave us without any archive at all.
        $collectedSize = array_sum(array_map(fn ($file) => $file->getSize(), File::allFiles($backupPath)));
        if ($collectedSize === 0) {
            throw new RuntimeException('Nothing to archive in ' . $backupPath);
        }

        $this->zipDirectory($backupPath, $partFilePath);

        // zip can report success and still leave nothing behind, e.g. when the disk fills up.
        if (!File::exists($partFilePath) || File::size($partFilePath) === 0) {
            throw new RuntimeException('Archive was not created: ' . $partFilePath);
        }

        Log::info($backupPath . '=>' . $zippedFilePath . '=>' . md5_file($partFilePath));

        return $partFilePath;
    }

    protected function zipDirectory(string $backupPath, string $partFilePath): void
    {
        $this->execShellCommand('cd ' . $backupPath . ' && zip -rm ' . $partFilePath . ' ./*', 'Zipping of ' . $backupPath);
        $this->execShellCommand('zip -T ' . $partFilePath, 'Integrity check of ' . $partFilePath);
    }

    /**
     * Puts the finished archive in place of the previous one. rename() is atomic within
     * a single filesystem, so a backup is only ever replaced by a complete archive.
     */
    protected function replaceArchive(string $partFilePath, string $zippedFilePath): void
    {
        if (!@rename($partFilePath, $zippedFilePath)) {
            // Some platforms refuse to rename onto an existing file.
            File::delete($zippedFilePath);

            if (!rename($partFilePath, $zippedFilePath)) {
                throw new RuntimeException('Unable to store backup ' . $zippedFilePath);
            }
        }

        Log::info('Backup stored ' . $zippedFilePath);
    }

    protected function notify(string $subject, string $message): void
    {
        $mails = array_filter((array) (config('boilerplate.system_admins_mail') ?: []));

        if (empty($mails)) {
            return;
        }

        // A broken mailer must never turn a finished backup into a failed job, nor mask
        // the error that made the backup fail in the first place.
        try {
            Mail::raw($message, function ($mail) use ($mails, $subject) {
                $mail->to($mails)->subject($subject . ' ' . config('app.name'));
            });
            Log::info('Sending Notification');
        } catch (Throwable $e) {
            Log::error('Backup notification could not be sent: ' . $e->getMessage());
        }
    }

    /**
     * @return array<int, string> lines the command wrote to stdout/stderr
     */
    protected function execShellCommand(string $command, string $description): array
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
