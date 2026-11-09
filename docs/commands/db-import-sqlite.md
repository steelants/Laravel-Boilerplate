# Database Migration

## db:import-sqlite

Moves an application's SQLite database into PostgreSQL or MySQL/MariaDB. A raw SQLite dump is not valid SQL for either, so only data travels: the schema is built from the application's migrations and every row is streamed over.

The import is all-or-nothing. It fills a staging area next to the target (PostgreSQL: schema `{schema}_import`, MySQL: database `{db}_import`) and swaps it in atomically only when complete. On any failure the staging area is dropped and the target is never touched.

### Signature

```bash
php artisan db:import-sqlite {path?} {--target=} {--except=*} {--force}
```

### Arguments and options

| Name | Description |
|------|-------------|
| `path` | Path to the SQLite file. Default: the first existing of `storage/database.sqlite`, `database/database.sqlite` |
| `--target` | Target connection from `config/database.php` (default: `DB_CONNECTION`) |
| `--except` | Table not to copy. A trailing `*` matches a prefix (`--except="search_*"`). Repeatable |
| `--force` | Skip the backup warning and confirmation (for scripts) |

### How it works

1. The source file is copied with `VACUUM INTO` (includes rows still in the WAL file) and the copy is migrated to the application's current migrations. The original is never modified. A source with migrations the application does not know is refused.
2. The staging schema/database is created and migrated.
3. Tables present on both sides are truncated in staging (migrations may seed defaults) and filled in batches. Foreign key checks are off meanwhile (PostgreSQL needs a superuser for `session_replication_role`).
4. SQLite booleans and blobs are converted for PostgreSQL, and id sequences are moved past the highest id.
5. Staging is swapped in for the live schema/database.

### Examples

```bash
php artisan db:import-sqlite
php artisan db:import-sqlite storage/old.sqlite --target=pgsql --except="documents_fts*" --force
```

> Make a backup of the target first (`pg_dump` / `mysqldump`); after a successful import it is the only undo.

---
