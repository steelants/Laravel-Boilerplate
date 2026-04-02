---
name: steelants-boilerplate-development
description: Build and work with the SteelAnts Laravel Boilerplate package (steelants/laravel-boilerplate). Use when generating CRUD, using boilerplate traits, the Alert/Menu facades, file uploads, activity logging, or settings on models.
---

# SteelAnts Laravel Boilerplate Development

## When to use this skill
- Generating CRUD scaffolding with `make:crud`
- Adding audit logging, file uploads, or settings to Eloquent models
- Using the `Alert` or `Menu` facades
- Registering navigation menus in middleware
- Working with Livewire CRUD controllers and data tables
- Configuring layouts, system admins, or backups

## Package overview

**Composer package:** `steelants/laravel-boilerplate`  
**Namespace:** `SteelAnts\LaravelBoilerplate`  
**Service provider:** `SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider`

Dependencies pulled in automatically:
- `livewire/livewire` ^3.0
- `steelants/form`, `steelants/modal`, `steelants/datatable`
- `steelants/laravel-auth`, `steelants/livewire-form`, `steelants/laravel-general`

---

## CRUD generation

### Command
```bash
php artisan make:crud {Model}
    [--namespace=/]          # Sub-namespace for controller and Livewire components
    [--force]                # Overwrite existing files
    [--full-page-components] # Generate form as a full-page Livewire component
```

### What it generates
- `App\Http\Controllers\{Namespace}\{Model}Controller` — uses `CRUD` or `CRUDFullPage` trait
- `App\Livewire\{Namespace}\{Model}\Form` — Livewire form component
- `App\Livewire\{Namespace}\{Model}\DataTable` — Livewire data table component
- Routes (resource-style)

### Controller traits

**`CRUD` trait** — standard list + inline form:
```php
use SteelAnts\LaravelBoilerplate\Traits\CRUD;

class PostController extends Controller
{
    use CRUD;

    protected string $model = Post::class; // optional; resolved from route if omitted
    public string $prefix = 'admin';       // route prefix, e.g. admin.post.index
    public array $views = ['index' => 'custom.index']; // override default view
    public string $layout = 'layout-app';  // override layout
}
```

**`CRUDFullPage` trait** — list + separate form page (extends `CRUD`):
```php
use SteelAnts\LaravelBoilerplate\Traits\CRUDFullPage;

class PostController extends Controller
{
    use CRUDFullPage;
    // adds form() method that renders a separate full-page view
}
```

---

## Model traits

### `Auditable` — automatic activity logging
```php
use SteelAnts\LaravelBoilerplate\Traits\Auditable;

class Post extends Model
{
    use Auditable;

    protected static string $nameColumn = 'title'; // column used in log messages (default: 'name')

    // Optional: limit which columns trigger an update log entry
    public function auditableColumns(): array
    {
        return ['title', 'status'];
    }

    // Optional: columns to ignore even if dirty
    public function auditableIgnored(): array
    {
        return ['updated_at'];
    }
}
```
Hooks into `created`, `updating`, `deleting` Eloquent events and writes to the `activities` table via the `Activity` model (polymorphic).

### `AuditableDetailed` — extended auditing
Same as `Auditable` but stores column-level diffs.

### `Fileable` — file attachments
```php
use SteelAnts\LaravelBoilerplate\Traits\Fileable;

class Post extends Model
{
    use Fileable;
}

// Relationships
$post->files;         // MorphMany — all files
$post->file;          // MorphOne — latest file

// Upload helpers (use in Livewire components)
$post->uploadFile($uploadedFile, rootPath: 'posts', public: true);
$post->replaceFile($uploadedFile);
```

### `HasSettings` — per-model key/value settings
```php
use SteelAnts\LaravelBoilerplate\Traits\HasSettings;

class User extends Model
{
    use HasSettings;
}

$user->getSettings('theme', 'light'); // returns value or default
$user->settings()->where('index', 'theme')->first();
```

### `SupportSystemAdmins` — system admin flag from config
```php
use SteelAnts\LaravelBoilerplate\Traits\SupportSystemAdmins;

class User extends Model
{
    use SupportSystemAdmins;
}

$user->is_system_admin; // bool; IDs come from config('boilerplate.system_admins') / APP_SYSTEM_ADMINS env
```

### `SystemPage` — switch Livewire component layout to system layout
```php
use SteelAnts\LaravelBoilerplate\Traits\SystemPage;

class MyPage extends Component
{
    use SystemPage; // sets $layout to config('boilerplate.layouts.system')
}
```

### `HasUsersPerPage` — per-user pagination limit in Livewire
```php
use SteelAnts\LaravelBoilerplate\Traits\HasUsersPerPage;

class MyTable extends Component
{
    use HasUsersPerPage;
    // sets $this->itemsPerPage from auth()->user()->limitationSetting on mount
}
```

### `SearchableSelectbox` — Ajax selectbox support on Livewire forms
Add to Livewire form components that use the `<x-boilerplate::selectbox-ajax>` component.

---

## Facades

### `Alert` — flash / instant alerts
```php
use SteelAnts\LaravelBoilerplate\Facades\Alert;

// Types: 'success', 'error', 'warning', 'info'
Alert::add(type: 'success', text: 'Record saved.');
Alert::add(type: 'error',   text: 'Something went wrong.', mode: AlertModeType::INSTANT);

Alert::get();                          // returns alerts for current mode
Alert::get(AlertModeType::INSTANT);
```

`AlertModeType::RELOAD` — stored in flash session, shown after redirect  
`AlertModeType::INSTANT` — stored in `Session::now`, shown immediately

### `Menu` — navigation menu builder
Typically called inside `App\Http\Middleware\GenerateMenus` (registered automatically):

```php
use SteelAnts\LaravelBoilerplate\Facades\Menu;

// In middleware handle():
Menu::make('sidebar', function ($menu) {
    $menu->add('Dashboard', route('dashboard'), 'bi-speedometer2');
    $menu->add('Posts', route('posts.index'), 'bi-file-text');
});

// In Blade:
{{ Menu::get('sidebar') }}
```

---

## Blade components

All prefixed `boilerplate::`:

| Component | Tag | Description |
|-----------|-----|-------------|
| Breadcrumb | `<x-boilerplate::breadcrumb>` | Page breadcrumbs |
| Searchbox | `<x-boilerplate::searchbox>` | Text search input |
| Selectbox | `<x-boilerplate::selectbox>` | Static select input |
| Selectbox Ajax | `<x-boilerplate::selectbox-ajax>` | Ajax-powered select |
| Pie graph | `<x-boilerplate::pie-graph>` | Pie chart |
| Gantt | `<x-boilerplate::gantt>` | Gantt chart |

### Livewire components
- `boilerplate.file.gallery` — file gallery with upload
- `boilerplate.setting.form` — generic settings form

---

## Built-in models

All configurable via `config/boilerplate.php` under `models`:

| Key | Default class | Table |
|-----|--------------|-------|
| `activity` | `Activity` | `activities` |
| `file` | `File` | `files` |
| `setting` | `Setting` | `settings` |
| `session` | `Session` | `sessions` |
| `subscription` | `Subscription` | `subscriptions` |

---

## Configuration (`config/boilerplate.php`)

```php
return [
    // Comma-separated user IDs with system admin privileges
    'system_admins'       => explode(',', env('APP_SYSTEM_ADMINS', '')),
    'system_admins_mail'  => explode(',', env('APP_SYSTEM_ADMINS_MAIL', '')),

    // Scheduled backup (runs daily at 00:00)
    'backup' => [
        'database'      => env('BACKUP_DATABASE', true),
        'storage'       => env('BACKUP_STORAGE', true),
        'storage_paths' => explode(',', env('BACKUP_STORAGE_PATHS', 'app')),
        'enviroment'    => env('BACKUP_ENV', true),
    ],

    // Override default model classes
    'models' => [
        'activity'     => Activity::class,
        'file'         => File::class,
        'setting'      => Setting::class,
        'session'      => Session::class,
        'subscription' => Subscription::class,
    ],

    // Layout view names
    'layouts' => [
        'default' => 'layout-app',
        'system'  => 'layout-app',
    ],
];
```

---

## Artisan commands

| Command | Description |
|---------|-------------|
| `make:crud {Model}` | Generate CRUD scaffold |
| `boilerplate:install` | Run full installation |
| `boilerplate:make-basic-tests {Model}` | Generate basic feature tests |
| `dispatch:job {job}` | Manually dispatch a queued job |

---

## Typical setup checklist

1. `composer require steelants/laravel-boilerplate`
2. `php artisan vendor:publish --tag=boilerplate-config`
3. `php artisan vendor:publish --tag=boilerplate-migrations && php artisan migrate`
4. Set `APP_SYSTEM_ADMINS` in `.env` (comma-separated user IDs)
5. Create `App\Http\Middleware\GenerateMenus` and register menus there (auto-pushed to `web` group)
6. Use `make:crud {Model}` to scaffold new resources
