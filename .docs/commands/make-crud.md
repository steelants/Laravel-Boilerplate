# make:crud

Scaffolds a full CRUD resource for a given model: a Livewire DataTable, a Livewire Form, a controller, routes, and optionally Pest tests.

## Signature

```bash
php artisan make:crud {model} [--namespace=] [--force] [--advanced] [--full-page-components] [--tests]
```

## Arguments

| Argument | Description |
|----------|-------------|
| `model` | Model class name (must exist in `App\Models`). Case-insensitive. |

## Options

| Option | Default | Description |
|--------|---------|-------------|
| `--namespace` | `/` | Sub-namespace for generated components, e.g. `--namespace=\\Admin` → `App\Livewire\Admin\Post` |
| `--force` | false | Overwrite existing files without prompting |
| `--advanced` | false | Generate individual public properties and a custom Blade instead of using `FormComponent` |
| `--full-page-components` | false | Form opens as a standalone full-page route instead of a modal |
| `--tests` | false | Generate a Pest feature test file |

## What gets generated

| File | Location |
|------|----------|
| `Form.php` | `app/Livewire/{Model}/Form.php` |
| `form.blade.php` | `resources/views/livewire/{model}/form.blade.php` |
| `DataTable.php` | `app/Livewire/{Model}/DataTable.php` |
| `{Model}Controller.php` | `app/Http/Controllers/{Model}Controller.php` |
| Routes | Appended to `routes/web.php` |
| `{Model}CrudTest.php` | `tests/Feature/{Model}CrudTest.php` (with `--tests`) |

## Form modes

**Default (FormComponent):**
Uses `steelants/livewire-form`. Fields, validation rules and labels are resolved automatically from model `$fillable` and `$casts`. No manual property declarations needed.

**Advanced (`--advanced`):**
Generates individual public properties, a manual `mount()`, separate `store()` / `update()` methods, and a per-field Blade template. Use when fine-grained control is needed.

## Examples

```bash
# Basic CRUD
php artisan make:crud Post

# With Pest tests
php artisan make:crud Post --tests

# Admin namespace
php artisan make:crud Post --namespace=\\Admin

# Advanced mode with tests
php artisan make:crud Post --advanced --tests

# Full-page form (no modal)
php artisan make:crud Post --full-page-components

# Force overwrite
php artisan make:crud Post --force
```

## Requirements

- The model must exist at `App\Models\{Model}` before running the command.
- `$fillable` should be defined on the model; the command warns if it is empty.
