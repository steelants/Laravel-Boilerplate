# install:boilerplate

Copies boilerplate scaffolding (controllers, views, assets, config) into the Laravel project and registers routes, JS/SCSS imports, and runs migrations.

## Signature

```bash
php artisan install:boilerplate [--force] [--views-only] [--no-migration]
```

## Options

| Option | Description |
|--------|-------------|
| `--force` | Overwrite customized files without prompting |
| `--views-only` | Copy only views and frontend assets; skip controllers, config, routes, migrations |
| `--no-migration` | Skip running migrations |

## What it does

**Phase 1 — safe files** (new files or files not modified by the user since last install):
- Copied silently without prompting

**Phase 2 — customized files** (files the user has modified):
- Install pauses, displays a warning to commit changes, then prompts per file

**Additional steps (skipped with `--views-only`):**
- Updates `package.json` with required JS dependencies
- Copies `vite.config.js`
- Deletes `node_modules` and `package-lock.json`
- Appends boilerplate routes to `routes/web.php`
- Removes legacy `/* BOILERPLATE */` blocks from `bootstrap/app.php`
- Runs `php artisan migrate`

**Always runs:**
- `storage:link`, `optimize:clear`, `view:clear`, `config:clear`, `cache:clear`

## Install manifest

Progress is tracked in `storage/boilerplate_install.json`. Each installed file stores the SHA-256 hash of the package source at install time. On the next run:

| State | Behavior |
|-------|----------|
| File unchanged in both package and project | Skipped |
| Package updated file, user did not modify | Auto-updated |
| User modified file | Prompts before overwrite |
| File not tracked (pre-manifest install) | Prompts before overwrite |

## Examples

```bash
# Standard install or update
php artisan install:boilerplate

# Force-overwrite everything including customized files
php artisan install:boilerplate --force

# Update only views and assets (preserve controllers and config)
php artisan install:boilerplate --views-only

# Install without running migrations
php artisan install:boilerplate --no-migration
```
