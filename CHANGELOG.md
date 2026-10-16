# Changelog

## 3.0.1

### Fixed

- `stubs/routes.stub`: added the missing `system.jobs.rerun` and `system.jobs.stop` routes. The prefab
  `JobsController` and `system/job/index.blade.php` already used them, so the Jobs page failed with
  `RouteNotFoundException` after installing the prefabs.
- Destructive DataTable actions are now consistent (`fa-trash`, red text, red icon):
  `Job\DataTable` `stop` and `User\DataTable` `remove`.
- Import order in prefab Livewire components (`Job\Form`, `Job\Trace`, `Subscription\Form`, `User\Form`).

### Added

- `User\DataTable` shows a check icon next to a verified e-mail (`renderColumnEmail`).
- `lang/cs.json` with the `E-mail verified` translation; the service provider now loads JSON translations
  from the package `lang` directory.
- Tests guarding prefab routes, destructive actions, the e-mail column and `JobsController` flash messages.

### Upgrade notes

See [docs/updates/upgrade-3_0.md](docs/updates/upgrade-3_0.md#v30---v301).
