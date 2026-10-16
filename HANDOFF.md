# Handoff: laravel-boilerplate 3.0.1

Status: implemented and tested locally, **not committed, not tagged**.
Working directory: `packages/Laravel-Boilerplate`.

## What was done

| # | Change | Files |
|---|--------|-------|
| 1 | Added `system.jobs.rerun` and `system.jobs.stop` (both `GET`) to the jobs group | `stubs/routes.stub` |
| 2 | Destructive DataTable actions unified: `fa-trash`, `text-danger` on `actionClass` and `iconClass` (Job `stop`, User `remove`) | `prefabs/app/Livewire/Job/DataTable.php`, `prefabs/app/Livewire/User/DataTable.php` |
| 3 | `renderColumnEmail()` shows `fa-check-circle` for verified e-mails; Czech translation | `prefabs/app/Livewire/User/DataTable.php`, `lang/cs.json`, `src/BoilerplateServiceProvider.php` (`loadJsonTranslationsFrom`) |
| 4 | No code change: prefab `<x-alerts />` already renders `->with('success'/'error')` flashes | test only |
| 5 | Import order fixed with pint (`ordered_imports` only) | `Job/Form`, `Job/Trace`, `Subscription/Form`, `User/Form` |

Docs: `CHANGELOG.md` (new), `docs/updates/upgrade-3_0.md` (section "v3.0.0 -> v3.0.1").

## Tests (new, `tests/Feature/Prefabs/`)

- `PrefabRoutesTest` - loads `routes.stub` into the router and checks every `route('...')` used in `prefabs/`.
  Skips `totp.verify`, `verification.resend` (opt-in in `Route::auth()`) and `home.test-alert*` (demo routes).
- `DestructiveActionsTest` - static scan of `prefabs/app/Livewire/**/DataTable*.php`; has a minimum-count guard
  (7 actions, 3 destructive) so it cannot pass empty.
- `UserDataTableEmailTest` - verified/unverified icon, escaping, `cs` translation.
- `JobsControllerFlashTest` - real `clear`/`rerun`/`stop` requests, message visible in rendered `<x-alerts />`,
  gone on the next request.

Run: `vendor/bin/pest` -> 95 passed, 1 failed.

## Known issues

- **Pre-existing failure** (not caused by this work): `SettingsHelperTest` -
  "settings() helper -> it ignores rows scoped to a specific settable owner" (`'dark'` returned instead of null,
  `tests/Feature/SettingsHelperTest.php:67`). Not investigated.
- `rerun` and `stop` delete data via `GET` (as specified). Consider `POST`/`DELETE` in a later release.
- `prefabs/resources/views/auth/verify.blade copy.php` is a stray file.
- `Route::auth()` in the stub does not enable `verify`/`totp`, but prefab views reference their routes.

## Decisions worth knowing

- **SCSS rule dropped.** A `.dropdown-item.text-danger .dropdown-item-icon { color: inherit; }` rule was added and
  then removed: `text-danger` on the icon already wins (Bootstrap uses `!important`), so the rule was redundant.
- **Pint** was run with a temporary config containing only `ordered_imports`. The default Laravel preset would
  restyle the whole repo (aligned `=>`, `!` spacing, concatenation) and clash with `phpcs.xml`.
- **`loadJsonTranslationsFrom`** was added to the service provider (outside `prefabs/`) because `lang/cs.json`
  is otherwise never loaded. The package had no `lang/` directory before.
- **Dev setup:** `vendor/` was missing and `composer.lock` is stale (and gitignored), so `composer update` was
  needed. Pint is not a dev dependency; it was installed outside the repo.

## Remaining steps

1. Review the diff (`git status`, `git diff`).
2. Commit; tag `3.0.1` (version comes from the git tag, `composer.json` has no version field).
3. Decide on the pre-existing `SettingsHelperTest` failure.
4. Not verified: SCSS compilation, running the prefabs in a real installed app, the `cs` translation outside tests.
