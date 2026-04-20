# job:dispatch

Dispatches a job by class name. Looks up the job first in `App\Jobs`, then falls back to `SteelAnts\LaravelBoilerplate\Jobs`.

## Signature

```bash
php artisan job:dispatch {job}
```

## Arguments

| Argument | Description |
|----------|-------------|
| `job` | Short class name of the job (without namespace), e.g. `Backup` |

## Examples

```bash
# Dispatch the built-in Backup job
php artisan job:dispatch Backup

# Dispatch a custom app job
php artisan job:dispatch ProcessInvoices
```
