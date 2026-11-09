<?php

use Illuminate\Support\Facades\File;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;
use SteelAnts\LaravelBoilerplate\Services\BackupService;

/*
 * Runs the real Backup job with the real zip binary (database dump is turned off), so these
 * tests prove what ends up in the archive and - above all - that the live storage the job
 * zips in place is never modified or deleted.
 */

beforeEach(function () {
    if (!trim((string) shell_exec('command -v zip')) || !trim((string) shell_exec('command -v unzip'))) {
        $this->markTestSkipped('zip/unzip binary is not installed');
    }

    config()->set('boilerplate.backup.database', false);
    config()->set('boilerplate.backup.storage', true);
    config()->set('boilerplate.backup.enviroment', false);
    config()->set('boilerplate.backup.storage_paths', ['app/backup-test']);
    config()->set('boilerplate.system_admins_mail', '');

    File::deleteDirectory(storage_path('backups'));
    File::deleteDirectory(storage_path('app/backup-test'));
    File::deleteDirectory(storage_path('backup-test-top'));
    File::ensureDirectoryExists(storage_path('backups'), 0755, true);

    $this->envDirectory = storage_path('framework/testing/backup-env');
    File::ensureDirectoryExists($this->envDirectory, 0755, true);
    File::put($this->envDirectory . '/.env', "APP_KEY=base64:backup-test\n");
    app()->useEnvironmentPath($this->envDirectory);
});

afterEach(function () {
    File::deleteDirectory(storage_path('backups'));
    File::deleteDirectory(storage_path('app/backup-test'));
    File::deleteDirectory(storage_path('backup-test-top'));
    File::deleteDirectory($this->envDirectory);
});

/**
 * Creates a small tree with nested directories, a hidden file, a name with spaces, an empty
 * file and an already compressed file, returns the absolute path of the tree.
 */
function seedLiveStorage(string $relativePath = 'app/backup-test'): string
{
    $root = storage_path($relativePath);

    File::ensureDirectoryExists($root . '/projects/1/files', 0755, true);
    File::ensureDirectoryExists($root . '/empty-dir', 0755, true);
    File::put($root . '/readme.txt', 'top level file');
    File::put($root . '/.hidden', 'hidden file');
    File::put($root . '/projects/1/files/report final.pdf', str_repeat('pdf-content ', 500));
    File::put($root . '/projects/1/files/photo.jpg', random_bytes(2048));
    File::put($root . '/projects/1/empty.txt', '');

    return $root;
}

/**
 * relative path => md5 of every file below $root, plus the list of directories.
 */
function snapshotTree(string $root): array
{
    $snapshot = ['files' => [], 'directories' => []];

    foreach (File::allFiles($root, true) as $file) {
        $snapshot['files'][$file->getRelativePathname()] = md5_file($file->getPathname());
    }
    foreach (File::directories($root) as $directory) {
        $snapshot['directories'][] = $directory;
    }

    ksort($snapshot['files']);
    sort($snapshot['directories']);

    return $snapshot;
}

function storageArchivePath(): string
{
    return storage_path('backups/' . date('Y-m-d') . '_storage.zip');
}

/**
 * @return array<int, string> entry names stored in the archive
 */
function archiveEntries(string $archive): array
{
    exec('zipinfo -1 ' . escapeshellarg($archive), $entries, $resultCode);
    expect($resultCode)->toBe(0);

    return $entries;
}

function extractArchive(string $archive): string
{
    $target = storage_path('backups/extracted');
    File::deleteDirectory($target);
    File::ensureDirectoryExists($target, 0755, true);

    exec('unzip -q -o ' . escapeshellarg($archive) . ' -d ' . escapeshellarg($target), $output, $resultCode);
    expect($resultCode)->toBe(0);

    return $target;
}

describe('live storage stays untouched', function () {
    it('does not delete or modify any file of the backed up storage', function () {
        $root = seedLiveStorage();
        $before = snapshotTree($root);

        (new Backup())->handle();

        expect(snapshotTree($root))->toBe($before);
        expect(File::isDirectory($root . '/empty-dir'))->toBeTrue();
    });

    it('keeps the storage when the same backup runs twice in a row', function () {
        $root = seedLiveStorage();
        $before = snapshotTree($root);

        (new Backup())->handle();
        (new Backup())->handle();

        expect(snapshotTree($root))->toBe($before);
    });

    it('keeps the .env file in place when it is backed up', function () {
        seedLiveStorage();
        config()->set('boilerplate.backup.enviroment', true);
        $envFile = $this->envDirectory . '/.env';
        $before = md5_file($envFile);

        (new Backup())->handle();

        expect(File::exists($envFile))->toBeTrue();
        expect(md5_file($envFile))->toBe($before);
    });

    it('keeps the storage and the previous archive when the archive check fails', function () {
        $root = seedLiveStorage();
        $before = snapshotTree($root);
        File::put(storageArchivePath(), 'good storage backup');

        $job = new class extends Backup
        {
            protected function verifyArchive(string $partFilePath): void
            {
                throw new RuntimeException('integrity check failed on purpose');
            }
        };

        expect(fn () => $job->handle())->toThrow(RuntimeException::class, 'integrity check failed on purpose');

        expect(snapshotTree($root))->toBe($before);
        expect(File::get(storageArchivePath()))->toBe('good storage backup');
        expect(File::glob(storage_path('backups') . '/*.part'))->toBeEmpty();
    });

    it('fails without touching anything when a storage path does not exist', function () {
        $root = seedLiveStorage();
        $before = snapshotTree($root);
        File::put(storageArchivePath(), 'good storage backup');
        config()->set('boilerplate.backup.storage_paths', ['app/backup-test', 'app/does-not-exist']);

        expect(fn () => (new Backup())->handle())->toThrow(RuntimeException::class, 'does not exist');

        expect(snapshotTree($root))->toBe($before);
        expect(File::get(storageArchivePath()))->toBe('good storage backup');
    });

    it('refuses storage paths that point at the backups or outside the storage', function (string $path) {
        $root = seedLiveStorage();
        $before = snapshotTree($root);
        File::put(storageArchivePath(), 'good storage backup');
        config()->set('boilerplate.backup.storage_paths', [$path]);

        expect(fn () => (new Backup())->handle())->toThrow(RuntimeException::class, 'Invalid backup storage path');

        expect(snapshotTree($root))->toBe($before);
        expect(File::get(storageArchivePath()))->toBe('good storage backup');
    })->with(['', '.', '/', 'backups', 'backups/tmp', 'app/../..', '../storage']);
});

describe('archive layout', function () {
    it('stores storage under its path relative to storage/, never as an absolute path', function () {
        seedLiveStorage();

        (new Backup())->handle();

        $entries = archiveEntries(storageArchivePath());

        expect($entries)->toContain('app/backup-test/readme.txt');
        expect($entries)->toContain('app/backup-test/projects/1/files/report final.pdf');

        $storageRoot = ltrim(storage_path(), '/');
        foreach ($entries as $entry) {
            expect($entry)->not->toStartWith('/');
            expect($entry)->not->toStartWith('./');
            expect($entry)->not->toContain($storageRoot);
            expect($entry)->not->toStartWith('backups/');
        }
    });

    it('puts env.backup into the archive root next to the storage', function () {
        seedLiveStorage();
        config()->set('boilerplate.backup.enviroment', true);

        (new Backup())->handle();

        $entries = archiveEntries(storageArchivePath());

        expect($entries)->toContain('env.backup');
        expect($entries)->toContain('app/backup-test/readme.txt');
    });

    it('archives only env.backup when storage is turned off', function () {
        seedLiveStorage();
        config()->set('boilerplate.backup.storage', false);
        config()->set('boilerplate.backup.enviroment', true);

        (new Backup())->handle();

        expect(archiveEntries(storageArchivePath()))->toBe(['env.backup']);
    });

    it('extracts to exactly the same files and contents as the live storage', function () {
        $root = seedLiveStorage();
        config()->set('boilerplate.backup.enviroment', true);

        (new Backup())->handle();

        $extracted = extractArchive(storageArchivePath());

        expect(snapshotTree($extracted . '/app/backup-test')['files'])->toBe(snapshotTree($root)['files']);
        expect(File::get($extracted . '/env.backup'))->toBe("APP_KEY=base64:backup-test\n");
    });

    it('keeps several and top level storage paths side by side', function () {
        seedLiveStorage('app/backup-test');
        seedLiveStorage('backup-test-top');
        config()->set('boilerplate.backup.storage_paths', ['app/backup-test', 'backup-test-top/']);

        (new Backup())->handle();

        $entries = archiveEntries(storageArchivePath());

        expect($entries)->toContain('app/backup-test/readme.txt');
        expect($entries)->toContain('backup-test-top/readme.txt');
    });

    it('cleans up the temporary copies and leaves no part file', function () {
        seedLiveStorage();
        config()->set('boilerplate.backup.enviroment', true);

        (new Backup())->handle();

        expect(File::allFiles(storage_path('backups/tmp')))->toBeEmpty();
        expect(File::glob(storage_path('backups') . '/*.part'))->toBeEmpty();
    });
});

describe('execShellCommand exit codes', function () {
    it('accepts an exit code that is explicitly allowed', function () {
        expect(BackupService::execShellCommand('exit 18', 'Zipping of storage', [0, 18]))->toBe([]);
    });

    it('fails on any other exit code', function () {
        expect(fn () => BackupService::execShellCommand('exit 12', 'Zipping of storage', [0, 18]))
            ->toThrow(RuntimeException::class, 'exit code 12');
        expect(fn () => BackupService::execShellCommand('exit 18', 'Zipping of storage'))
            ->toThrow(RuntimeException::class, 'exit code 18');
    });
});

describe('execShellCommand memory', function () {
    it('keeps only the tail of a huge output instead of every line', function () {
        $memoryBefore = memory_get_usage();

        // One line per file is what zip/unzip/cp print - here a million of them.
        $output = BackupService::execShellCommand('seq 1 1000000', 'Huge output');

        expect($output)->toHaveCount(50);
        expect($output[0])->toBe('999951');
        expect(end($output))->toBe('1000000');
        expect(memory_get_usage() - $memoryBefore)->toBeLessThan(1024 * 1024);
    });

    it('puts the tail of stderr into the error message', function () {
        expect(fn () => BackupService::execShellCommand('seq 1 1000; echo "disk full" >&2; exit 2', 'Zipping of storage'))
            ->toThrow(RuntimeException::class, 'Zipping of storage failed (exit code 2): 952');
        expect(fn () => BackupService::execShellCommand('echo "disk full" >&2; exit 2', 'Zipping of storage'))
            ->toThrow(RuntimeException::class, 'disk full');
    });
});
