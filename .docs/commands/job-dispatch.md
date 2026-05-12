# Job Management

## job:dispatch

Dispatches a job by class name from the CLI. Looks up the job in `App\Jobs`, then falls back to `SteelAnts\LaravelBoilerplate\Jobs` and `SteelAnts\LaravelBoilerplate\Dashboard\Jobs`.

### Signature

```bash
php artisan job:dispatch {job}
```

### Arguments

| Argument | Description |
|----------|-------------|
| `job` | Short class name of the job (without namespace), e.g. `Backup` |

### Examples

```bash
# Dispatch the built-in Backup job
php artisan job:dispatch Backup

# Dispatch a custom app job
php artisan job:dispatch ProcessInvoices
```

---

## Manual run via UI — `#[AllowManualRun]`

The Jobs admin page (`/system/jobs`) lists only jobs that carry the `#[AllowManualRun]` attribute. The same guard applies to the Livewire dispatch form — attempting to open the form for a job without the attribute returns **403**.

### Marking a job as manually runnable

```php
use SteelAnts\LaravelBoilerplate\Attributes\AllowManualRun;

#[AllowManualRun]
class ProcessInvoices implements ShouldQueue
{
    // ...
}
```

### Registering additional scan paths

By default `JobHelper` scans:

- `app/Jobs`
- `packages/Laravel-Boilerplate/src/Jobs`
- `packages/Laravel-Boilerplate.Dashboard/src/Jobs`
- `vendor/steelants/laravel-boilerplate/src/Jobs`
- `vendor/steelants/laravel-boilerplate.dashboard/src/Jobs`

To add a custom path (e.g. from a module), call `registerPath()` in your `AppServiceProvider`:

```php
use SteelAnts\LaravelBoilerplate\Helpers\JobHelper;

public function boot(): void
{
    JobHelper::registerPath(base_path('modules/Billing/Jobs'));
}
```

To add a custom namespace for class resolution (used when dispatching via the form):

```php
JobHelper::registerNamespace('Modules\\Billing\\Jobs\\');
```
