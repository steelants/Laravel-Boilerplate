<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Illuminate\Support\Facades\File;
use RuntimeException;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;

/**
 * Backup job with the shell calls replaced by plain PHP, so the fail safe ordering can be
 * tested without rm/cp/zip/mysqldump being installed. Push a step name into $failing to
 * make that step fail: 'database', 'storage', 'environment' or 'zip:<part file name>'.
 */
class BackupFixture extends Backup
{
    /** @var array<int, string> */
    public array $failing = [];

    public function callCreateArchive(string $backupPath, string $zippedFilePath): string
    {
        return $this->createArchive($backupPath, $zippedFilePath);
    }

    public function callReplaceArchive(string $partFilePath, string $zippedFilePath): void
    {
        $this->replaceArchive($partFilePath, $zippedFilePath);
    }

    protected function execShellCommand(string $command, string $description): array
    {
        return [];
    }

    protected function prepareTmpDirectory(string $backupPath): void
    {
        File::ensureDirectoryExists($backupPath, 0755, true);
        File::cleanDirectory($backupPath);
    }

    protected function backupDatabase(string $db_backup_path, string $date): void
    {
        $this->failWhenRequested('database');
        File::put($db_backup_path . '/database_' . $date . '.sql', 'dump');
    }

    protected function backupStorage(string $fs_backup_path): void
    {
        $this->failWhenRequested('storage');
        File::put($fs_backup_path . '/app.txt', 'storage');
    }

    protected function backupEnvironment(string $fs_backup_path): void
    {
        $this->failWhenRequested('environment');
        File::put($fs_backup_path . '/env.backup', 'APP_KEY=testing');
    }

    protected function zipDirectory(string $backupPath, string $partFilePath): void
    {
        $this->failWhenRequested('zip:' . basename($partFilePath));

        File::put($partFilePath, 'fresh archive');
        File::cleanDirectory($backupPath); // zip -m moves the source files into the archive
    }

    private function failWhenRequested(string $step): void
    {
        if (in_array($step, $this->failing, true)) {
            throw new RuntimeException($step . ' failed on purpose');
        }
    }
}
