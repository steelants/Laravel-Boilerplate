# Menu Builder

SteelAnts Laravel-Boilerplate provides a menu builder for application navigation.

Menus are registered using the `Menu` facade, typically inside the published `App\Http\Middleware\GenerateMenus` middleware.


## Defining Items

Items are defined fluently with `MenuItem::make()`, the same way actions are defined with
`Action::make()`. The definition is a `PendingMenuItem`; the builder turns it into the runtime
item (`MenuItemLink` for a route, `MenuItemAction` for an action, plain `MenuItem` otherwise)
through `MenuBuilder::createMenuItem()`, so a custom builder still decides the item classes.

```php
use SteelAnts\LaravelBoilerplate\Facades\Menu;
use SteelAnts\LaravelBoilerplate\Support\MenuItem;

Menu::make('main-menu', fn () => [
    MenuItem::make('home')->label(__('Home'))->icon('fas fa-home')->route('home'),

    MenuItem::make('tasks')->label(__('Tasks'))->icon('fas fa-list')->route('tasks.index')
        ->can('tasks')
        ->items([
            MenuItem::make('tasks.all')->label(__('All'))->route('tasks.index'),
            MenuItem::make('tasks.open')->label(__('Open'))->route('tasks.index', ['filter' => 9]),
        ])
        ->dropdown([
            MenuItem::make('tasks.create')->label(__('Create'))->icon('fas fa-plus')
                ->action('wire:click="create"'),
        ]),

    MenuItem::make('system')->label(__('System'))->as('toggle')
        ->visible(fn () => Gate::allows('is-system-admin'))
        ->items([
            MenuItem::make('system.user')->label(__('Users'))->icon('fas fa-users')->route('system.user.index'),
        ]),
]);
```

| Method | Description |
|---|---|
| `make(string $id)` | Unique item identifier |
| `label(string\|Closure)` | Item title |
| `icon(string\|Closure)` | Icon class (e.g. Font Awesome) |
| `route(string $name, array $parameters = [])` | Item links to a named route |
| `action(string $attributes)` | Item is a button with the given HTML attributes (`wire:click="..."`) |
| `parameters(array)` | Route parameters |
| `confirm(?string)` | Confirmation text (option `confirm`) |
| `as(string)` | Render style (option `as`), e.g. `toggle` for a collapsible group |
| `option(key, value)` / `options(array)` | Any other option for the views |
| `visible(bool\|Closure)` | Condition; closures are resolved by the container |
| `can(string $ability, mixed $arguments = [])` | Ability checked via `$user->can()` (respects an overridden `User::can()`, falls back to `Gate` for guests); may be repeated |
| `items(array\|MenuSet\|Closure)` | Sub items |
| `dropdown(array\|MenuSet\|Closure)` | Dropdown items of the item |

Hidden items are skipped together with their children, `null` entries in the arrays are
ignored, so `$condition ? MenuItem::make(...) : null` works too. Conditions are evaluated
once, when the menu is built.

Definitions can be added to an existing menu or item with `define()` (`defineDropdown()`
for an item's dropdown):

```php
(Menu::get('main-menu') ?? Menu::make('main-menu'))->define([
    MenuItem::make('reports')->label(__('Reports'))->route('reports.index'),
]);
```


## Menu Sets

A whole menu (or a part of it) can be a class, the menu counterpart of `ActionSet`:

```php
use SteelAnts\LaravelBoilerplate\Support\MenuItem;
use SteelAnts\LaravelBoilerplate\Support\MenuSet;

class TaskMenu extends MenuSet
{
    protected function define(): array
    {
        return [
            MenuItem::make('tasks.all')->label(__('All tasks'))->route('tasks.index'),
            MenuItem::make('tasks.mine')->label(__('My tasks'))->route('tasks.index', ['assignees_ids' => auth()->id()]),
        ];
    }
}

Menu::make('main-menu', AppMenu::class);                       // resolved from the container
MenuItem::make('tasks')->route('tasks.index')->items(new TaskMenu); // as sub items
```

`Menu::make()` accepts a `MenuSet` (instance or class name), an array of definitions, or a
callback. The callback receives the `MenuBuilder` and may either return definitions or fill
the builder imperatively.


## Array API

The original API is still available; `add()` takes the same options the fluent definition
produces:

```php
Menu::make('main-menu', function ($menu) {
    $mainItem = $menu->add('Home', [
        'id'    => 'home',
        'icon'  => 'fas fa-eye',
        'route' => 'general.index',
    ]);

    $mainItem->addRoute('Dashboard', 'home-dashboard', 'general.sub-index', 'fas fa-eye');
    $mainItem->addDropdownAction('Delete', 'home-delete', 'wire:click="delete"', 'fas fa-trash', options: ['confirm' => 'Sure?']);
});
```


## Active State

Every route item answers two questions, both used by the published navigation component:

| Method | True when |
|---|---|
| `isActive()` | The item points exactly at the current page: same route name and matching parameters. |
| `isUse()` | The current page lives below the item: the route name is the same or a descendant (`tasks.index` also covers `tasks.edit` and `tasks.show`), parameters still have to match. |

Parameters are compared against both the route parameters and the query string of the
current request, but only on the keys that actually distinguish the items on that menu
level - the item's own parameter keys plus the keys declared by siblings pointing at the
same route.

```php
$menu->define([
    MenuItem::make('tasks-all')->label('All')->route('tasks.index'),
    MenuItem::make('tasks-open')->label('Open')->route('tasks.index', ['filter' => 9]),
    MenuItem::make('tasks-closed')->label('Closed')->route('tasks.index', ['filter' => 10]),
]);
```

On `/tasks?filter=9&page=2` only `Open` is active: `page` is not declared by any of the
three items, so paging, sorting and fulltext never break the highlight, while `All` is
not active because `filter` is a key its siblings distinguish on.


## Rendering

The menu is rendered by the published navigation component in the application layout.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Alerts](alerts.md)
- [Components](components.md)
