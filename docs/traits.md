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

Without an explicit `rootPath`, the file is stored under `{prefix}/{model}/{id}` (joined with `DIRECTORY_SEPARATOR`) — `$public` decides the disk (`public` or `local`) it's written to.

There is no `disk` column — which disk a file lives on is never persisted. Reading it back (`$file->getLink()`, deleting) resolves the disk dynamically by checking where the file actually exists (`FileCollector::resolveDisk()`, `Storage::disk('public')->exists(...)`). Pass `$public` explicitly to `getLink()` when the caller already knows it, to skip that check.

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

The prefix (set once, e.g. by Laravel-Tenant via `FileStorage::setPrefix()`) always comes first — including when you pass an explicit `rootPath` — so a tenant prefix `tenant_media/1` plus `Task::filePath()` above resolves to `tenant_media/1/tasks/1/file.txt`, and `uploadFile($file, rootPath: 'posts')` under that same prefix resolves to `tenant_media/1/posts/file.txt`.

Uploads without an owning model (`FileStorage::uploadFileAnonymouse()`) default their root to `uploads` — the prefix still applies the same way: `{prefix}/uploads/file.txt`.

### FileCollector / FileStorage facade

The underlying class is `SteelAnts\LaravelBoilerplate\Support\FileCollector` — lives in `Support/` (facade-backed, like `AlertCollector`/`MenuCollector`), not `Services/` (that's for plain services with no facade, e.g. `ActivityService`). It's registered as a singleton so state (`$prefix`) is shared across every access path within a request.

Consumer-facing call sites (`Fileable`, `Models\File::getLink()`, `Livewire\File\Gallery`) go through the `FileStorage` facade:

```php
use SteelAnts\LaravelBoilerplate\Facades\FileStorage;

FileStorage::uploadFileAnonymouse($uploadedFile);
```

Other packages that just need to configure it (not consume the upload API), like Laravel-Tenant setting the prefix, resolve `FileCollector` directly instead — guarded with `class_exists()` since Laravel-Tenant has no hard dependency on Laravel-Boilerplate:

```php
if (class_exists(FileCollector::class)) {
    app(FileCollector::class)->setPrefix('tenant_media/' . $tenant->id);
}
```

The pre-refactor `SteelAnts\LaravelBoilerplate\Services\FileService` class still exists as a deprecated shim — static calls (`FileService::uploadFile(...)`) forward to the `FileStorage` facade, so old code keeps working without any changes.

The old static `FileService::method()` calls still work (forwarded to the container-bound instance via `__callStatic`) but are deprecated — prefer `app(FileService::class)` or the facade, per the rule above.


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
