# CRUD Generation

SteelAnts Laravel-Boilerplate can generate complete CRUD resources for your models.

A generated resource consists of a controller, a Livewire DataTable, a Livewire Form, routes and optionally tests.


## Generating a Resource

The model must exist in `App\Models` before running the command.

Generate the resource:

```bash
php artisan make:crud Post
```

Available options:

| Option | Description |
|---|---|
| `--namespace=` | Sub-namespace for the controller and Livewire components |
| `--force` | Overwrite existing files |
| `--full-page-components` | Generate the form as a full page Livewire component |
| `--advanced` | Generate a fully customizable form with individual properties |
| `--tests` | Generate a Pest feature test file |

For the full command reference see:

[make:crud command](commands/make-crud.md)


## Form Modes

**Default** - minimal boilerplate powered by steelants/livewire-form:

```php
class Form extends FormComponent
{
    use HasModel;

    public $modelClass = Post::class;
}
```

Fields, validation rules and labels are resolved automatically from the model `$fillable` and `$casts`.

**Advanced** (`--advanced`) - fully explicit form:

```php
class Form extends Component
{
    public $model;
    public string $name;
    public string $action = 'store';
}
```

Individual public properties per field with explicit `mount()`, `store()` and `update()` methods.


## Controller Traits

**CRUD** - standard list with an inline form:

```php
use SteelAnts\LaravelBoilerplate\Traits\CRUD;

class PostController extends Controller
{
    use CRUD;

    protected string $model = Post::class;
}
```

Optional properties:

| Property | Description |
|---|---|
| `$model` | Model class; resolved from the route when omitted |
| `$prefix` | Route prefix, e.g. `admin.post.index` |
| `$views` | Override default views per action |
| `$layout` | Override the layout |

**CRUDFullPage** - list with a separate form page:

```php
use SteelAnts\LaravelBoilerplate\Traits\CRUDFullPage;

class PostController extends Controller
{
    use CRUDFullPage;
}
```


## Generated Tests

With `--tests` the command generates a Pest feature test covering:

- Guest redirect from the index page
- Authenticated access to the index page
- Create, update and delete through the Livewire components

Test values are generated from the model casts.

Fields ending in `_id` are marked with a `TODO` comment and should be replaced with the related factory.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Model Traits](traits.md)
- [Commands](commands.md)
- [Testing](testing.md)
