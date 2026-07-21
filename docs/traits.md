# Model Traits

SteelAnts Laravel-Boilerplate provides traits for common model and component functionality.


## Auditable

Automatic activity logging for Eloquent models.

```php
use SteelAnts\LaravelBoilerplate\Traits\Auditable;

class Post extends Model
{
    use Auditable;

    protected static string $nameColumn = 'title';
}
```

The trait hooks into the `created`, `updating` and `deleting` events and writes to the `activities` table.

Optional methods:

```php
public function auditableColumns(): array
{
    return ['title', 'status'];
}
```

```php
public function auditableIgnored(): array
{
    return ['updated_at'];
}
```

| Method | Description |
|---|---|
| `auditableColumns()` | Only listed columns trigger an update log entry |
| `auditableIgnored()` | Columns ignored even when changed |

The `$nameColumn` property defines the column used in log messages (default `name`).


## AuditableDetailed

Same as `Auditable` but stores column level diffs.

```php
use SteelAnts\LaravelBoilerplate\Traits\AuditableDetailed;
```


## Fileable

File attachments for Eloquent models.

```php
use SteelAnts\LaravelBoilerplate\Traits\Fileable;

class Post extends Model
{
    use Fileable;
}
```

Relationships:

```php
$post->files; // MorphMany - all files
$post->file;  // MorphOne - latest file
```

Upload helpers:

```php
$post->uploadFile($uploadedFile, rootPath: 'posts', public: true); // rootPath is optional
$post->replaceFile($fileModel, $uploadedFile);
```

Without an explicit `rootPath`, the file is stored under `{prefix}/{model}/{id}` (joined with `DIRECTORY_SEPARATOR`) — `$public` decides the disk (`public` or `local`), which is persisted on the `files.disk` column so links always resolve to the right disk.

The `{model}/{id}` part can be overridden per-model by defining a `filePath()` method — useful when you want a different folder shape than the default:

```php
class Task extends Model
{
    use Fileable;

    public function filePath(): string
    {
        return 'tasks/' . $this->id;
    }
}
```

The prefix (set once, e.g. by Laravel-Tenant via `FileStorage::setPrefix()`) always comes first, so a tenant prefix `tenant_media/1` plus `Task::filePath()` above resolves to `tenant_media/1/tasks/1/file.txt`.

### FileStorage facade

The underlying `FileService` is registered as a singleton and exposed through the `FileStorage` facade — use it directly for anonymous uploads (no owning model) or when you need `setPrefix()`:

```php
use SteelAnts\LaravelBoilerplate\Facades\FileStorage;

FileStorage::setPrefix('tenant_media/' . $tenant->id); // e.g. done by Laravel-Tenant
FileStorage::uploadFileAnonymouse($uploadedFile, 'uploads');
```

The old static `FileService::method()` calls still work (forwarded to the container-bound instance) but are deprecated — prefer the `FileStorage` facade or `app(FileService::class)`.


## HasSettings

Per-model key/value settings.

```php
use SteelAnts\LaravelBoilerplate\Traits\HasSettings;

class User extends Model
{
    use HasSettings;
}
```

Reading settings:

```php
$user->getSettings('theme', 'light');
```

When the default is `null`, the value falls back to `config('setting_field.theme.value')`.


## SupportSystemAdmins

System admin flag resolved from the configuration.

```php
use SteelAnts\LaravelBoilerplate\Traits\SupportSystemAdmins;

class User extends Model
{
    use SupportSystemAdmins;
}
```

```php
$user->is_system_admin; // bool
```

Admin user IDs are defined by the `APP_SYSTEM_ADMINS` environment variable.


## SystemPage

Switches a Livewire component to the system layout.

```php
use SteelAnts\LaravelBoilerplate\Traits\SystemPage;

class MyPage extends Component
{
    use SystemPage;
}
```

The layout is defined by `config('boilerplate.layouts.system')`.


## HasUsersPerPage

Per-user pagination limit for Livewire components.

```php
use SteelAnts\LaravelBoilerplate\Traits\HasUsersPerPage;

class MyTable extends Component
{
    use HasUsersPerPage;
}
```


## SearchableSelectbox

Support for the Ajax selectbox on Livewire form components.

```php
use SteelAnts\LaravelBoilerplate\Traits\SearchableSelectbox;
```

Use together with the `<x-boilerplate::selectbox-ajax>` component.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [CRUD Generation](crud.md)
- [Components](components.md)
