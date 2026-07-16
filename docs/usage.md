# Usage

SteelAnts Laravel-Boilerplate provides a ready to use application skeleton.

After installation your application contains:

- A responsive Bootstrap template with light and dark theme
- Authentication pages (provided by steelants/laravel-auth)
- System management pages (users, jobs, cache, backups, logs, settings, audit)
- Base controllers, Livewire components and views in the `App` namespace for customization


## System Pages

The installer publishes system controllers under `App\Http\Controllers\System`:

- Users
- Jobs
- Cache
- Backups
- Logs
- Settings
- Audit
- API tokens

System pages are protected by the `IsSystemAdmin` middleware.

System administrators are defined by user ID in the configuration:

```env
APP_SYSTEM_ADMINS=1,2
```


## Navigation

Menus are registered in the published `App\Http\Middleware\GenerateMenus` middleware.

For more information see:

[Menu Builder documentation](menu.md)


## Alerts

Notifications are dispatched using the `Alert` facade:

```php
Alert::add(type: 'success', text: 'Record saved.');
```

For more information see:

[Alerts documentation](alerts.md)


## CRUD Resources

Generate a complete CRUD resource for an existing model:

```bash
php artisan make:crud Post
```

The command generates a controller, Livewire DataTable, Livewire Form, routes and optionally tests.

For more information see:

[CRUD Generation documentation](crud.md)


## Model Traits

The package provides traits for common model functionality:

| Trait | Description |
|---|---|
| `Auditable` | Automatic activity logging |
| `Fileable` | File attachments |
| `HasSettings` | Per-model key/value settings |
| `SupportSystemAdmins` | System admin flag from config |

For the full list see:

[Model Traits documentation](traits.md)


## UI Components

Blade components are available under the `boilerplate::` prefix:

```blade
<x-boilerplate::breadcrumb :items="['Home' => route('home')]" />
```

For the full list see:

[Components documentation](components.md)


## Next Steps

Continue with:

- [Configuration](configuration.md)
- [Menu Builder](menu.md)
- [Alerts](alerts.md)
- [CRUD Generation](crud.md)
