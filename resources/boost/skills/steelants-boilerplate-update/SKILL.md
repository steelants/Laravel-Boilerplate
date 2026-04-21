---
name: steelants-boilerplate-update
description: Update an existing SteelAnts Laravel Boilerplate installation (steelants/laravel-boilerplate). Use when upgrading the package in an already installed project, re-syncing prefab files, running new migrations, or recovering from a partial install.
---

# SteelAnts Laravel Boilerplate Update

## When to use this skill
- Upgrading `steelants/laravel-boilerplate` to a newer version in an existing project
- Re-syncing boilerplate prefab files after package update
- Updating only views/resources without touching app code or config
- Running migrations added in a new package release
- Recovering from a partial or interrupted install

---

## How the update mechanism works

The boilerplate tracks installed files via a manifest at `storage/boilerplate_install.json`.  
Each entry maps a relative file path to the SHA-256 hash of the source prefab at install time.

On each run of `install:boilerplate` the command compares three hashes:

| State | Source hash | Stored hash | Dest hash | Action |
|-------|-------------|-------------|-----------|--------|
| Up to date | = | = | = | skipped |
| New package version | ≠ | any | any | auto-updated (if dest unmodified) |
| User-customized | any | ≠ | ≠ | prompts before overwriting |
| Untracked (no manifest entry) | any | — | exists | prompts before overwriting |

---

## Recommended update workflow

```bash
# 1. Pull the new package version
composer update steelants/laravel-boilerplate

# 3. Run the update
php artisan install:boilerplate --force

# 4. Review what changed
git diff
```

---

## After the update checklist

1. `git diff` — review all changed files
2. Check `config/boilerplate.php` for new keys (compare with prefab source)
3. `php artisan migrate` — if you used `--no-migration`
4. `npm install && npm run build`
5. `php artisan optimize:clear`
6. Test authentication, CRUD, and any customized views
7. enshure any customization before update is still present (you need tu diferentiate betven package changes like updates of prefabs and repo specific changes)
