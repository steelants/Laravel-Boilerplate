# Upgrade Guide

## v2.6 -> v3.0

### Modal moved from `steelants/modal` into the boilerplate

The separate `steelants/modal` package is deprecated and archived. The modal is now part of
Laravel-Boilerplate and `steelants/modal` was removed from the package dependencies.

Laravel-Boilerplate 3.0 declares a `conflict` with `steelants/modal`, so Composer refuses to
install them side by side. That is deliberate - both packages register the Livewire component
`modal-basic` and whichever service provider boots last wins. If the old one wins, components
marked with the new `AllowInModal` attribute are refused and the modal silently stays closed.

**Action required:**

1. Remove the package from your application:

   ```bash
   composer remove steelants/modal
   ```

2. Upgrade every SteelAnts package that still requires it. All `steelants/laravel-boilerplate.*`
   addons are released as 3.x alongside this version:

   ```bash
   composer update steelants/*
   ```

3. Remove `SteelAnts\Modal\ModalServiceProvider::class` from `bootstrap/providers.php` if it was
   registered manually. Nothing has to be registered instead - `BoilerplateServiceProvider` takes
   care of the modal.

4. Replace the `AllowInModal` import in every Livewire component you open in a modal:

   ```php
   - use SteelAnts\Modal\Livewire\Attributes\AllowInModal;
   + use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;
   ```

   **This step is mandatory.** The old attribute is no longer recognised. A component that keeps
   the old import is simply not allowed in the modal - nothing throws, the modal stays closed.
   Search your project for `SteelAnts\Modal` before upgrading.

5. Optionally rename the component in your layout:

   ```blade
   - @livewire('modal-basic', key('modal'))
   + @livewire('boilerplate.modal.basic', key('modal'))
   ```

   The legacy name `modal-basic` stays registered, so this step is not required.

### `AllowInModal` ability has to be a named argument

`#[AllowInModal('is-system-admin')]` passed the string into the first parameter `bool $asGuest`.
PHP coerced it to `true`, so the gate was never evaluated and the component could be opened by a
guest. Use the named argument:

```php
- #[AllowInModal('is-system-admin')]
+ #[AllowInModal(ability: 'is-system-admin')]
```

The boilerplate normalizes the old positional form to `ability` on its own, so components that were
unintentionally open to guests are now gated. Check that the ability you declared really is the one
you want enforced.

### Modal view namespace changed

The view moved from `modal::modal-basic` to `boilerplate::livewire.modal.basic`. If you published
or overrode the modal view, move it to
`resources/views/vendor/boilerplate/livewire/modal/basic.blade.php`.

---

### `DatatableComponent` components require some locked properties

Component properties $sortableColumns, $searchableColumns and $viewName must be decorade with #[Locked]


## v3.0.0 -> v3.0.1

### State-changing system routes are `POST` now

The following routes in `routes/web.php` (originally generated from `stubs/routes.stub`) changed
from `GET` to `POST`, because a `GET` request must not change state (CSRF, link prefetching, crawlers):

| Route name              | URI                          |
|-------------------------|------------------------------|
| `system.jobs.clear`     | `/system/jobs/clear`         |
| `system.jobs.rerun`     | `/system/jobs/rerun`         |
| `system.jobs.stop`      | `/system/jobs/stop`          |
| `system.cache.clear`    | `/system/cache/clear`        |
| `system.log.clear`      | `/system/log/clear`          |
| `system.log.delete`     | `/system/log/delete/{file}`  |
| `system.backup.run`     | `/system/backup/run`         |
| `system.backup.delete`  | `/system/backup/delete/{backup_date}` |

Change `Route::get(...)` to `Route::post(...)` for each of them. Downloads, `index`, `detail` and
`tail` routes stay `GET`.

If you published or overrode the prefab views (`system/job`, `system/cache`, `system/log`,
`system/backup`), replace the `<a href>` / `window.location.href` links with the new component,
which renders a POST form with `@csrf` and an optional confirm dialog:

```blade
<x-boilerplate::form-button
    :action="route('system.log.delete', ['file' => $filename])"
    :confirm="__('Are you sure?')"
    :title="__('Remove')" />
```

Props: `action`, `confirm`, `label`, `title`, `icon` (default `fas fa-trash`), `variant` (default `danger`).
Any other attribute (e.g. `class="btn-sm"`) is merged onto the button.

## v3.0.5 -> v3.1.0

### API page: OpenAPI / Insomnia / Restfox export

The system API page (`system.api.index`) can now download the API routes as an OpenAPI 3 spec, an Insomnia
collection or a Restfox collection. Exports never contain a token, only a `token` variable. Request fields are detected from `FormRequest` type-hints of the actions.

`ApiExport` lives in the package (`SteelAnts\LaravelBoilerplate\Support\ApiExport`), no file to install. Updated prefabs (installed with `install:boilerplate`):
`app/Http/Controllers/System/ApiController.php` and `resources/views/system/api/index.blade.php`.

Add the route to the `system` group of `routes/web.php`:

```php
Route::get('/api/export/{format}', [App\Http\Controllers\System\ApiController::class, 'export'])->whereIn('format', ['openapi', 'insomnia', 'restfox'])->name('api.export');
```

Run `php artisan install:boilerplate` to get the new files. Files you have customized are listed
and you decide whether to replace them. If you customized `ApiController` or the API view, merge the
`export` and `apiRoutes` parts and the download buttons by hand.

Then add the route above and clear the route cache (`php artisan route:clear`).

### New command `db:import-sqlite`

Replaces a PostgreSQL or MySQL/MariaDB database with the data of a SQLite file. No action is needed,
the command is registered by the package. See [db:import-sqlite](../commands/db-import-sqlite.md).

```bash
php artisan db:import-sqlite storage/database.sqlite --target=pgsql
```

The schema is built from your migrations and the swap is atomic, nothing changes on failure. Make a backup
of the target first. Tables that must not be copied (e.g. SQLite FTS5 shadow tables) are skipped with
`--except="table_prefix*"`; rebuild such indexes yourself afterwards.
