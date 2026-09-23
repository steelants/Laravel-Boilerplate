# Menu Builder

SteelAnts Laravel-Boilerplate provides a menu builder for application navigation.

Menus are registered using the `Menu` facade, typically inside the published `App\Http\Middleware\GenerateMenus` middleware.


## Single Level Menu

Example:

```php
use SteelAnts\LaravelBoilerplate\Facades\Menu;

Menu::make('main-menu', function ($menu) {
    $systemRoutes = [
        'general' => ['fas fa-eye', 'general.index'],
    ];

    foreach ($systemRoutes as $title => $route_data) {
        $icon = $route_data[0];
        $route = $route_data[1];

        $menu->add($title, [
            'id' => strtolower($title),
            'icon' => $icon,
            'route' => $route,
        ]);
    }
});
```


## Multi Level Menu

Sub items are added to the item returned by `add()`:

```php
Menu::make('main-menu', function ($menu) {
    $mainItem = $menu->add('Home', [
        'id' => strtolower('Home'),
        'icon' => 'fas fa-eye',
        'route' => 'general.index',
    ]);

    $mainItem->add('Dashboard', [
        'id' => strtolower('Home-Dashboard'),
        'icon' => 'fas fa-eye',
        'route' => 'general.sub-index',
    ]);
});
```


## Item Options

| Option | Description |
|---|---|
| `id` | Unique item identifier |
| `icon` | Icon class (e.g. Font Awesome) |
| `route` | Route name the item links to |


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
$menu->addRoute('All', 'tasks-all', 'tasks.index');
$menu->addRoute('Open', 'tasks-open', 'tasks.index', parameters: ['filter' => 9]);
$menu->addRoute('Closed', 'tasks-closed', 'tasks.index', parameters: ['filter' => 10]);
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
