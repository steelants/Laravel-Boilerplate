<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

/**
 * Moves the app's SQLite database into PostgreSQL or MySQL/MariaDB.
 *
 * A raw SQLite dump isn't valid SQL for either, so only data travels: the
 * schema is built from the application's migrations and every row is
 * streamed over. All or nothing — the import fills a staging area next to
 * the target (PostgreSQL: schema `{schema}_import`, MySQL: database
 * `{db}_import`) and only a complete one is swapped in atomically; on any
 * failure the staging area is dropped and the target was never touched.
 * (migrate:fresh can't run inside a transaction: db:wipe disconnects.)
 */
class ImportSqliteDatabase extends Command
{
    protected $signature = 'db:import-sqlite
        {path? : Path to the SQLite file (default: the first existing of the usual locations)}
        {--target= : Target connection from config/database.php (default: DB_CONNECTION)}
        {--except=* : Table (or table prefix ending in *, e.g. "search_*") that is not copied; repeatable}
        {--force : Skip the backup warning and confirmation (for scripts)}';

    protected $description = 'Replace the PostgreSQL/MySQL database with the whole SQLite database (nothing changes on failure)';

    private const STAGING = 'sqlite_import_staging';

    /** Stays below PostgreSQL's 65 535 bound-parameter limit per statement. */
    private const MAX_PARAMETERS = 60000;

    /** Rows per INSERT, bounded so huge tables never build a huge batch. */
    private const MAX_ROWS = 500;

    /** Bytes per INSERT, well under MySQL's default max_allowed_packet (64 MB). */
    private const MAX_BYTES = 8 * 1024 * 1024;

    public function handle(): int
    {
        set_time_limit(0);

        $path = $this->sqlitePath();
        if ($path === null) {
            $this->error('SQLite file not found. Pass its path as an argument.');

            return self::FAILURE;
        }

        $targetName = $this->option('target') ?? config('database.default');
        $target = DB::connection($targetName);

        if (! in_array($target->getDriverName(), ['pgsql', 'mysql', 'mariadb'], true)) {
            $this->error("Target connection \"{$targetName}\" is neither PostgreSQL nor MySQL/MariaDB.");

            return self::FAILURE;
        }

        $this->info("Source: {$path}");
        $this->info("Target: {$targetName} ({$target->getDatabaseName()})");

        if (! $this->option('force')) {
            $this->newLine();
            $this->warn('ALL data in the target database will be REPLACED by the SQLite data.');
            $this->warn('The import is all-or-nothing, but make a backup of the target first anyway');
            $this->warn('(e.g. pg_dump / mysqldump) — a backup is the only undo once it has succeeded.');
            $this->newLine();

            if (! $this->confirm("Do you have a backup and want to replace \"{$target->getDatabaseName()}\"?", false)) {
                $this->info('Aborted, nothing was changed.');

                return self::FAILURE;
            }
        }

        $copy = $this->migratedCopy($path);
        if ($copy === null) {
            return self::FAILURE;
        }

        $isPostgres = $target->getDriverName() === 'pgsql';
        $live = $isPostgres ? $target->getSchemaBuilder()->getCurrentSchemaName() : $target->getDatabaseName();
        $staging = "{$live}_import";

        try {
            $this->createStaging($target, $targetName, $staging);
            $this->fill(DB::connection('sqlite_import'), DB::connection(self::STAGING));
            DB::purge(self::STAGING);

            $isPostgres ? $this->swapSchemas($target, $live, $staging) : $this->swapDatabases($target, $live, $staging);
        } catch (Throwable $exception) {
            $this->newLine();
            $this->error('Import failed, the target database was left unchanged: '.$exception->getMessage());

            return self::FAILURE;
        } finally {
            DB::purge(self::STAGING);
            $target->statement($isPostgres ? "DROP SCHEMA IF EXISTS \"{$staging}\" CASCADE" : "DROP DATABASE IF EXISTS `{$staging}`");

            DB::purge('sqlite_import');
            foreach (['', '-wal', '-shm'] as $suffix) {
                @unlink($copy.$suffix);
            }
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    private function sqlitePath(): ?string
    {
        $candidates = $this->argument('path') !== null ? [$this->argument('path')] : [
            storage_path('database.sqlite'),
            database_path('database.sqlite'),   // config/database.php default
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && filesize($candidate) > 0) {
                return realpath($candidate);
            }
        }

        return null;
    }

    /**
     * The source may be on an older schema than the application. Rows are only
     * copied once both sides are on the same migrations: a temporary copy
     * of the source is migrated up to the application's version — the
     * original file is never touched. A source with migrations this code
     * doesn't know (newer than the code) is refused.
     */
    private function migratedCopy(string $path): ?string
    {
        $copy = tempnam(sys_get_temp_dir(), 'boilerplate-import-');
        unlink($copy);

        // VACUUM INTO, unlike a file copy, includes rows still sitting in
        // the -wal file (the app runs SQLite in WAL mode).
        (new PDO('sqlite:'.$path))->exec('VACUUM INTO '.(new PDO('sqlite::memory:'))->quote($copy));

        config(['database.connections.sqlite_import' => ['driver' => 'sqlite', 'database' => $copy, 'prefix' => '']]);

        $migrator = $this->laravel['migrator'];
        $known = array_keys($migrator->getMigrationFiles([database_path('migrations'), ...$migrator->paths()]));
        $unknown = array_diff(DB::connection('sqlite_import')->table('migrations')->pluck('migration')->all(), $known);

        if ($unknown !== []) {
            $this->error('The source has migrations the application does not know (is it newer?): '.implode(', ', $unknown));
            DB::purge('sqlite_import');
            unlink($copy);

            return null;
        }

        $this->call('migrate', ['--database' => 'sqlite_import', '--force' => true]);

        return $copy;
    }

    /**
     * An empty schema/database next to the live one, reachable through the
     * STAGING connection (a copy of the target's config pointing at it).
     */
    private function createStaging(Connection $target, string $targetName, string $staging): void
    {
        $config = config("database.connections.{$targetName}");

        if ($target->getDriverName() === 'pgsql') {
            $target->statement("DROP SCHEMA IF EXISTS \"{$staging}\" CASCADE");
            $target->statement("CREATE SCHEMA \"{$staging}\"");
            config(['database.connections.'.self::STAGING => [...$config, 'search_path' => $staging]]);
        } else {
            $target->statement("DROP DATABASE IF EXISTS `{$staging}`");
            $target->statement("CREATE DATABASE `{$staging}` CHARACTER SET {$config['charset']} COLLATE {$config['collation']}");
            config(['database.connections.'.self::STAGING => [...$config, 'database' => $staging]]);
        }
    }

    /** PostgreSQL DDL is transactional, so the two renames happen as one. */
    private function swapSchemas(Connection $target, string $live, string $staging): void
    {
        $target->transaction(function () use ($target, $live, $staging) {
            $target->statement("ALTER SCHEMA \"{$live}\" RENAME TO \"{$live}_previous\"");
            $target->statement("ALTER SCHEMA \"{$staging}\" RENAME TO \"{$live}\"");
            $target->statement("DROP SCHEMA \"{$live}_previous\" CASCADE");
        });
    }

    /**
     * MySQL commits on DDL, but a single RENAME TABLE moving many tables is
     * atomic: the live tables go aside into `{db}_previous` and the
     * imported ones in.
     */
    private function swapDatabases(Connection $target, string $live, string $staging): void
    {
        $previous = "{$live}_previous";

        try {
            $target->statement("DROP DATABASE IF EXISTS `{$previous}`");
            $target->statement("CREATE DATABASE `{$previous}`");

            $renames = [
                ...array_map(fn (string $table) => "`{$live}`.`{$table}` TO `{$previous}`.`{$table}`", $this->tables($target)),
                ...array_map(fn (string $table) => "`{$staging}`.`{$table}` TO `{$live}`.`{$table}`", $target->getSchemaBuilder()->getTableListing($staging, false)),
            ];
            $target->statement('RENAME TABLE '.implode(', ', $renames));
        } finally {
            $target->statement("DROP DATABASE IF EXISTS `{$previous}`");
        }
    }

    /**
     * Tables of the connection's own schema/database only — a bare
     * getTableListing() lists every schema the user can see.
     *
     * @return list<string>
     */
    private function tables(Connection $connection): array
    {
        $schema = $connection->getSchemaBuilder();

        return $schema->getTableListing($schema->getCurrentSchemaName(), false);
    }

    /**
     * Builds $target's schema from migrations and streams every row of
     * every table both sides share into it.
     */
    private function fill(Connection $source, Connection $target): void
    {
        $source->disableQueryLog();
        $target->disableQueryLog();

        $this->call('migrate', ['--database' => self::STAGING, '--force' => true]);

        // The target's own fresh migration history stays. Tables listed in
        // --except (e.g. SQLite FTS5 shadow tables) are neither emptied nor copied.
        $targetTables = array_values(array_filter(
            array_diff($this->tables($target), ['migrations']),
            fn (string $table): bool => ! $this->isExcluded($table),
        ));
        $tables = array_values(array_intersect($this->tables($source), $targetTables));

        // Tables are copied in no particular order (and self-referencing
        // foreign keys point within their own table), so FK checks are off meanwhile.
        // PostgreSQL's "replica" role needs a superuser.
        $isPostgres = $target->getDriverName() === 'pgsql';
        $target->statement($isPostgres ? 'SET session_replication_role = replica' : 'SET FOREIGN_KEY_CHECKS = 0');

        // Some migrations seed defaults — the source
        // already has its own copy of those rows.
        foreach ($targetTables as $table) {
            $target->table($table)->truncate();
        }

        $rowCounts = array_map(fn (string $table) => $source->table($table)->count(), array_combine($tables, $tables));

        $progress = $this->output->createProgressBar(array_sum($rowCounts));
        $progress->setFormat(' %current%/%max% rows [%bar%] %percent:3s%% · %elapsed:6s% · %message%');
        $progress->setMessage('');
        $progress->start();

        foreach ($tables as $table) {
            $progress->setMessage("{$table} ({$rowCounts[$table]})");
            $this->copyTable($source, $target, $table, fn (int $rows) => $progress->advance($rows));
        }

        $progress->setMessage('done');
        $progress->finish();
        $this->newLine();

        $target->statement($isPostgres ? 'SET session_replication_role = DEFAULT' : 'SET FOREIGN_KEY_CHECKS = 1');

        if ($isPostgres) {
            $this->resetSequences($target, $tables);
        }
    }

    private function isExcluded(string $table): bool
    {
        foreach ((array) $this->option('except') as $pattern) {
            if ($pattern === $table || (str_ends_with($pattern, '*') && str_starts_with($table, substr($pattern, 0, -1)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Streams rows through a cursor and inserts them in batches capped by
     * row count, bound parameters and bytes, so memory stays flat however
     * large the table is.
     *
     * @param  callable(int): void  $onBatch  called with each inserted batch's row count
     */
    private function copyTable(Connection $source, Connection $target, string $table, callable $onBatch): void
    {
        $types = collect($target->getSchemaBuilder()->getColumns($table))->pluck('type_name', 'name')->all();
        $columns = array_values(array_intersect($source->getSchemaBuilder()->getColumnListing($table), array_keys($types)));

        $maxRows = min(self::MAX_ROWS, intdiv(self::MAX_PARAMETERS, max(1, count($columns))));
        $batch = [];
        $batchBytes = 0;

        foreach ($source->table($table)->select($columns)->orderByRaw('rowid')->cursor() as $row) {
            $row = (array) $row;
            $batchBytes += array_sum(array_map(fn ($value) => is_string($value) ? strlen($value) : 8, $row));
            $batch[] = $this->convert($row, $types);

            if (count($batch) >= $maxRows || $batchBytes >= self::MAX_BYTES) {
                $target->table($table)->insert($batch);
                $onBatch(count($batch));
                $batch = [];
                $batchBytes = 0;
            }
        }

        if ($batch !== []) {
            $target->table($table)->insert($batch);
            $onBatch(count($batch));
        }
    }

    /**
     * SQLite keeps booleans as 0/1 and blobs as plain strings. PostgreSQL
     * wants real booleans, and bytea must be bound as a stream (PDO LOB);
     * MySQL takes both as they are.
     *
     * @param  array<string, mixed>  $row
     * @param  array<string, string>  $types  column => target type name
     * @return array<string, mixed>
     */
    private function convert(array $row, array $types): array
    {
        foreach ($row as $column => $value) {
            if ($value === null) {
                continue;
            }

            if ($types[$column] === 'bool') {
                $row[$column] = $value ? 'true' : 'false';
            } elseif ($types[$column] === 'bytea') {
                $stream = fopen('php://memory', 'r+b');
                fwrite($stream, $value);
                rewind($stream);
                $row[$column] = $stream;
            }
        }

        return $row;
    }

    /**
     * Explicit ids were inserted, so each PostgreSQL id sequence is moved
     * past the highest one (MySQL's AUTO_INCREMENT follows on its own).
     *
     * @param  list<string>  $tables
     */
    private function resetSequences(Connection $target, array $tables): void
    {
        foreach ($tables as $table) {
            if (! $target->getSchemaBuilder()->hasColumn($table, 'id')) {
                continue;
            }

            // Tables with a string id (e.g. job_batches) have no sequence.
            $sequence = $target->selectOne("SELECT pg_get_serial_sequence(?, 'id') AS name", [$table])->name;
            if ($sequence === null) {
                continue;
            }

            $wrapped = $target->getQueryGrammar()->wrapTable($table);
            $target->statement("SELECT setval(?, COALESCE((SELECT MAX(id) FROM {$wrapped}), 0) + 1, false)", [$sequence]);
        }
    }
}
