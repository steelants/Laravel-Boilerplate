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
use Illuminate\Support\Str;
use RuntimeException;
use SteelAnts\LaravelBoilerplate\Attributes\AllowManualRun;
use SteelAnts\LaravelBoilerplate\Services\BackupService;
use SteelAnts\LaravelBoilerplate\Services\SystemAdminNotificationService;
use SteelAnts\LaravelBoilerplate\Types\BackupStatusType;
use Throwable;

#[AllowManualRun()]
#[Timeout(600)]
#[Tries(1)]
class Backup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Fast compression - the default level costs a lot of CPU for a few percent. Files that
     * are compressed already are only stored.
     */
    private const ZIP_COMPRESSION = '-1 -n .zip:.gz:.tgz:.bz2:.xz:.7z:.rar:.jpg:.jpeg:.png:.gif:.webp:.heic:.mp3:.mp4:.mov:.avi:.mkv:.webm:.docx:.xlsx:.pptx:.odt:.ods';

    public function __construct() {}

    public function handle()
    {
        // Two runs would share backups/tmp and wipe each other's files, and a restore running
        // at the same time would end up in the archive - whoever queued the job, it steps aside.
        // The job uuid is unique per dispatch and is known again in failed(); a direct handle() call has no job.
        $lock = BackupService::backupLock($this->job?->uuid() ?? (string) Str::uuid());
        if (!$lock->get()) {
            Log::warning('Backup skipped, another backup or restore is already running');

            // Only a backup queued from the modal waits for this job; a running one owns the status.
            if ((BackupService::status()['state'] ?? null) === BackupStatusType::QUEUED) {
                BackupService::setStatus(BackupStatusType::FAILED, __('Backup skipped, another backup or restore is already running'));
            }

            return;
        }

        try {
            $this->runBackup();
        } finally {
            $lock->release();
        }
    }

    protected function runBackup(): void
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
            BackupService::setStatus(BackupStatusType::FINISHED, __('Backup skipped, every component is disabled'));

            return;
        }

        $partFiles = [];
        $storedFiles = [];
        $storageSources = [];
        $startedAt = now()->timestamp;

        BackupService::setStatus(BackupStatusType::RUNNING, null, ['started_at' => $startedAt]);

        try {
            foreach ([$db_backup_path, $fs_backup_path] as $backupPath) {
                $this->prepareTmpDirectory($backupPath);
            }

            if ($backupDatabase) {
                $this->backupDatabase($db_backup_path, $date);
            }

            if ($backupStorage) {
                // Storage is zipped straight from its place, see createArchive().
                $storageSources = $this->storageSources();
            }

            if ($backupEnvironment) {
                $this->backupEnvironment($fs_backup_path);
            }

            // Build every archive under a temporary name first, ...
            foreach ($archives as $name => $backupPath) {
                $zippedFilePath = storage_path('backups/' . $date . '_' . $name . '.zip');
                $partFiles[$zippedFilePath] = $this->createArchive($backupPath, $zippedFilePath, $name === 'storage' ? $storageSources : []);
            }

            // ... and replace the previous backup only once all of them are complete.
            foreach ($partFiles as $zippedFilePath => $partFile) {
                $this->replaceArchive($partFile, $zippedFilePath);
                unset($partFiles[$zippedFilePath]);
                $storedFiles[] = ['name' => basename($zippedFilePath), 'size' => File::size($zippedFilePath)];
            }
        } catch (Throwable $e) {
            foreach ($partFiles as $partFile) {
                File::delete($partFile);
            }

            Log::error('Backup failed: ' . $e->getMessage());

            // Status and notification are handled in failed(), which runs for queued and sync dispatch alike.
            throw $e;
        }

        BackupService::setStatus(BackupStatusType::FINISHED, null, ['started_at' => $startedAt, 'files' => $storedFiles]);
        SystemAdminNotificationService::send(__('Backup Run successfully'), __('Backup Run successfully'));
    }

    /**
     * Runs for any failure, including a timeout or a killed worker.
     */
    public function failed(?Throwable $e): void
    {
        // A killed worker never reached the finally in handle(); only this job's own lock is released.
        if ($this->job?->uuid()) {
            BackupService::backupLock($this->job->uuid())->release();
        }

        BackupService::setStatus(BackupStatusType::FAILED, $e?->getMessage());
        SystemAdminNotificationService::send(__('Backup Failed'), __('Backup failed') . ': ' . $e?->getMessage());
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

            $this->execShellCommand($this->lowPriority() . "cp $dbFile $backupFile 2>&1", 'Backup of ' . $dbName);
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
                $command = $this->lowPriority() . "pg_dump --no-comments $parameters -h $dbHost -U $dbUserName -d $dbName -f \"$backupFile\" 2>&1";

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
            // --single-transaction dumps InnoDB from a snapshot instead of locking every table
            // (it turns --lock-tables off), so the application keeps working during the dump;
            // --quick streams rows instead of buffering whole tables in memory.
            $command = $this->lowPriority() . 'mysqldump --skip-ssl --skip-comments --no-tablespaces --single-transaction --quick ' . $parameters . ' -h ' . $dbHost . ' -u ' . $dbUserName . ' -p' . $dbPassword . ' ' . $dbName . " -r $backupFile 2>&1";

            $this->execShellCommand($command, 'Backup of ' . $dbName . ' ' . $type);
            Log::info('Backup ' . $dbName . ' db ' . $type);
        }
    }

    /**
     * Configured storage paths relative to storage_path(). They are zipped in place instead of
     * being copied to backups/tmp first - the copy read and wrote the whole storage once more
     * and needed the same amount of free disk space again.
     *
     * @return array<int, string>
     */
    protected function storageSources(): array
    {
        $sources = [];

        foreach (config('boilerplate.backup.storage_paths') ?? [] as $storagePath) {
            $relativePath = trim((string) $storagePath, " \t\n\r\0\x0B/");

            // The archive is written to storage/backups - zipping it into itself would never end.
            if ($relativePath === '' || $relativePath === '.' || $relativePath === 'backups' || str_starts_with($relativePath, 'backups/') || str_contains($relativePath, '..')) {
                throw new RuntimeException('Invalid backup storage path: ' . $storagePath);
            }

            if (!File::exists(storage_path($relativePath))) {
                throw new RuntimeException('Backup of storage/' . $relativePath . ' failed: path does not exist');
            }

            $sources[] = $relativePath;
        }

        return $sources;
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
    protected function createArchive(string $backupPath, string $zippedFilePath, array $storageSources = []): string
    {
        $partFilePath = $zippedFilePath . '.part';
        File::delete($partFilePath);

        // An empty source directory means an earlier step produced nothing - zip would
        // just report "Nothing to do" and leave us without any archive at all.
        $collectedSize = array_sum(array_map(fn ($file) => $file->getSize(), File::allFiles($backupPath)));
        if ($collectedSize === 0 && empty($storageSources)) {
            throw new RuntimeException('Nothing to archive in ' . $backupPath);
        }

        try {
            if (!empty($storageSources)) {
                $this->zipStoragePaths($storageSources, $partFilePath);
            }

            if ($collectedSize > 0) {
                $this->zipDirectory($backupPath, $partFilePath);
            }

            $this->verifyArchive($partFilePath);

            // zip can report success and still leave nothing behind, e.g. when the disk fills up.
            if (!File::exists($partFilePath) || File::size($partFilePath) === 0) {
                throw new RuntimeException('Archive was not created: ' . $partFilePath);
            }
        } catch (Throwable $e) {
            // The caller only knows about part files that were finished - a half written one
            // (possibly as big as the whole storage) would otherwise stay on the disk.
            File::delete($partFilePath);

            throw $e;
        }

        Log::info($backupPath . '=>' . $zippedFilePath . '=>' . md5_file($partFilePath));

        return $partFilePath;
    }

    /**
     * Zips the temporary files (dumps, env.backup) into the archive root. They are only
     * copies, so -m removes them once they are in the archive.
     */
    protected function zipDirectory(string $backupPath, string $partFilePath): void
    {
        $this->execShellCommand(
            'cd ' . escapeshellarg($backupPath) . ' && ' . $this->lowPriority() . 'zip -r -m -q ' . self::ZIP_COMPRESSION . ' ' . escapeshellarg($partFilePath) . ' ./* 2>&1',
            'Zipping of ' . $backupPath
        );
    }

    /**
     * Adds live storage paths to the archive. zip runs from storage_path() with relative
     * paths, so the archive holds "app/..." exactly where Restore looks for it - an absolute
     * path would end up as "home/.../storage/app/..." inside the archive.
     * Never pass -m here, it would delete the application's files.
     *
     * @param array<int, string> $storageSources
     */
    protected function zipStoragePaths(array $storageSources, string $partFilePath): void
    {
        $paths = implode(' ', array_map('escapeshellarg', $storageSources));

        // Exit code 18: a file disappeared or could not be read while zipping (the storage is
        // live). Everything else is in the archive, so this is a warning, not a failed backup.
        $this->execShellCommand(
            'cd ' . escapeshellarg(storage_path()) . ' && ' . $this->lowPriority() . 'zip -r -q ' . self::ZIP_COMPRESSION . ' ' . escapeshellarg($partFilePath) . ' ' . $paths . ' 2>&1',
            'Zipping of storage',
            [0, 18]
        );
    }

    protected function verifyArchive(string $partFilePath): void
    {
        $this->execShellCommand($this->lowPriority() . 'zip -T -q ' . escapeshellarg($partFilePath) . ' 2>&1', 'Integrity check of ' . $partFilePath);
    }

    /**
     * Prefix that runs a command with the lowest CPU and IO priority, so a backup does not
     * starve the web server and the database. Best-effort IO class (not idle), so a busy
     * disk still lets the backup finish within the job timeout.
     */
    protected function lowPriority(): string
    {
        static $prefix = null;

        if ($prefix === null) {
            $prefix = '';
            foreach (['nice' => 'nice -n 19 ', 'ionice' => 'ionice -c2 -n7 '] as $binary => $command) {
                exec('command -v ' . $binary . ' 2>/dev/null', $output, $resultCode);
                if ($resultCode === 0) {
                    $prefix .= $command;
                }
            }
        }

        return $prefix;
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

    /**
     * @param  array<int, int>    $allowedExitCodes exit codes that do not fail the backup
     * @return array<int, string> lines the command wrote to stdout/stderr
     */
    protected function execShellCommand(string $command, string $description, array $allowedExitCodes = [0]): array
    {
        return BackupService::execShellCommand($command, $description, $allowedExitCodes);
    }
}
