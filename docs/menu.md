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


## Rendering

The menu is rendered by the published navigation component in the application layout.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Alerts](alerts.md)
- [Components](components.md)
