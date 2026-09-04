<?php

use Illuminate\Support\Facades\File;
use SteelAnts\LaravelBoilerplate\Tests\Fixtures\BackupFixture;

beforeEach(function () {
    config()->set('boilerplate.backup.database', true);
    config()->set('boilerplate.backup.storage', true);
    config()->set('boilerplate.backup.enviroment', false);
    config()->set('boilerplate.backup.retention_days', 0);
    config()->set('boilerplate.system_admins_mail', '');

    File::deleteDirectory(storage_path('backups'));
    File::ensureDirectoryExists(storage_path('backups'), 0755, true);
});

afterEach(function () {
    File::deleteDirectory(storage_path('backups'));
});

function backupArchivePath(string $date, string $name): string
{
    return storage_path('backups/' . $date . '_' . $name . '.zip');
}

function seedBackupArchive(string $date, string $name, string $content = 'previous archive'): string
{
    $path = backupArchivePath($date, $name);
    File::put($path, $content);

    return $path;
}

function leftoverPartFiles(): array
{
    return File::glob(storage_path('backups') . '/*.part') ?: [];
}

describe('fail safe archive replacement', function () {
    it('keeps the previous archives when the database dump fails', function () {
        $date = date('Y-m-d');
        seedBackupArchive($date, 'database', 'good database backup');
        seedBackupArchive($date, 'storage', 'good storage backup');

        $job = new BackupFixture();
        $job->failing[] = 'database';

        expect(fn () => $job->handle())->toThrow(RuntimeException::class);

        expect(File::get(backupArchivePath($date, 'database')))->toBe('good database backup');
        expect(File::get(backupArchivePath($date, 'storage')))->toBe('good storage backup');
        expect(leftoverPartFiles())->toBeEmpty();
    });

    it('keeps the previous archives when zipping the second archive fails', function () {
        $date = date('Y-m-d');
        seedBackupArchive($date, 'database', 'good database backup');
        seedBackupArchive($date, 'storage', 'good storage backup');

        $job = new BackupFixture();
        $job->failing[] = 'zip:' . $date . '_storage.zip.part';

        expect(fn () => $job->handle())->toThrow(RuntimeException::class);

        // The database archive was already built, but nothing is swapped until every
        // archive of the run is complete.
        expect(File::get(backupArchivePath($date, 'database')))->toBe('good database backup');
        expect(File::get(backupArchivePath($date, 'storage')))->toBe('good storage backup');
        expect(leftoverPartFiles())->toBeEmpty();
    });

    it('replaces the previous archives once the new ones are complete', function () {
        $date = date('Y-m-d');
        seedBackupArchive($date, 'database', 'good database backup');
        seedBackupArchive($date, 'storage', 'good storage backup');

        (new BackupFixture())->handle();

        expect(File::get(backupArchivePath($date, 'database')))->toBe('fresh archive');
        expect(File::get(backupArchivePath($date, 'storage')))->toBe('fresh archive');
        expect(leftoverPartFiles())->toBeEmpty();
    });

    it('leaves the archive of a disabled component alone', function () {
        $date = date('Y-m-d');
        config()->set('boilerplate.backup.database', false);
        seedBackupArchive($date, 'database', 'good database backup');
        seedBackupArchive($date, 'storage', 'good storage backup');

        (new BackupFixture())->handle();

        expect(File::get(backupArchivePath($date, 'database')))->toBe('good database backup');
        expect(File::get(backupArchivePath($date, 'storage')))->toBe('fresh archive');
    });

    it('does nothing at all when every component is disabled', function () {
        $date = date('Y-m-d');
        config()->set('boilerplate.backup.database', false);
        config()->set('boilerplate.backup.storage', false);
        config()->set('boilerplate.backup.enviroment', false);
        seedBackupArchive($date, 'database', 'good database backup');

        (new BackupFixture())->handle();

        expect(File::get(backupArchivePath($date, 'database')))->toBe('good database backup');
    });

    it('archives the .env file together with storage', function () {
        config()->set('boilerplate.backup.storage', false);
        config()->set('boilerplate.backup.enviroment', true);

        (new BackupFixture())->handle();

        expect(File::exists(backupArchivePath(date('Y-m-d'), 'storage')))->toBeTrue();
        expect(File::exists(backupArchivePath(date('Y-m-d'), 'database')))->toBeTrue();
    });
});

describe('createArchive', function () {
    it('refuses to archive an empty directory', function () {
        $backupPath = storage_path('backups/tmp/empty');
        File::ensureDirectoryExists($backupPath, 0755, true);

        expect(fn () => (new BackupFixture())->callCreateArchive($backupPath, backupArchivePath('2026-09-04', 'database')))
            ->toThrow(RuntimeException::class, 'Nothing to archive');

        expect(leftoverPartFiles())->toBeEmpty();
    });

    it('overwrites a part file left behind by an earlier run', function () {
        $backupPath = storage_path('backups/tmp/db');
        File::ensureDirectoryExists($backupPath, 0755, true);
        File::put($backupPath . '/dump.sql', 'dump');

        $zippedFilePath = backupArchivePath('2026-09-04', 'database');
        File::put($zippedFilePath . '.part', 'truncated leftover');

        $partFilePath = (new BackupFixture())->callCreateArchive($backupPath, $zippedFilePath);

        expect($partFilePath)->toBe($zippedFilePath . '.part');
        expect(File::get($partFilePath))->toBe('fresh archive');
        expect(File::exists($zippedFilePath))->toBeFalse();
    });
});

describe('replaceArchive', function () {
    it('moves the part file over the previous archive', function () {
        $zippedFilePath = seedBackupArchive('2026-09-04', 'database', 'previous archive');
        $partFilePath = $zippedFilePath . '.part';
        File::put($partFilePath, 'fresh archive');

        (new BackupFixture())->callReplaceArchive($partFilePath, $zippedFilePath);

        expect(File::get($zippedFilePath))->toBe('fresh archive');
        expect(File::exists($partFilePath))->toBeFalse();
    });
});

describe('housekeeping', function () {
    // Dropping old archives is not this job's business - it only ever touches the archives
    // of the day it is running for.
    it('never touches archives of other days', function () {
        seedBackupArchive('2019-01-01', 'database', 'old database backup');
        seedBackupArchive('2019-01-01', 'storage', 'old storage backup');

        (new BackupFixture())->handle();

        expect(File::get(backupArchivePath('2019-01-01', 'database')))->toBe('old database backup');
        expect(File::get(backupArchivePath('2019-01-01', 'storage')))->toBe('old storage backup');
    });

    it('never touches archives of other days when the backup fails', function () {
        seedBackupArchive('2019-01-01', 'database', 'old database backup');

        $job = new BackupFixture();
        $job->failing[] = 'storage';

        expect(fn () => $job->handle())->toThrow(RuntimeException::class);

        expect(File::get(backupArchivePath('2019-01-01', 'database')))->toBe('old database backup');
    });
});
