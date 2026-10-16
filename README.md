<div align="center">

<a href="https://steelants.cz">
	<picture>
		<source
			media="(prefers-color-scheme: dark)"
			srcset="https://steelants.cz/wp-content/uploads/2026/07/white_3.png">
		<img
			src="https://steelants.cz/wp-content/themes/wp_steelants_v5/img/logo.png"
			alt="SteelAnts"
			width="180">
	</picture>
</a>

<h1>Laravel-Boilerplate</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/steelants/laravel-boilerplate.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-boilerplate) [![Total Downloads](https://img.shields.io/packagist/dt/steelants/laravel-boilerplate.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-boilerplate)

<p>
Everything you need to start your Laravel journey - application template, system management, CRUD generator and UI components built on Bootstrap and Livewire.
</p>

<p>
Created by <a href="https://steelants.cz">SteelAnts s.r.o.</a>
</p>

</div>

## Preview

[boilerplate.steelants.cz](https://boilerplate.steelants.cz)

## Features

SteelAnts Laravel-Boilerplate provides:

- Responsive app template (Bootstrap, Livewire, light/dark theme)
- User management
- Job management
- Cache management
- Backup manager
- Log viewer
- Audit log
- API routes page
- Menu builder
- Alert system
- CRUD generator
- UI components (tabs, breadcrumb, gantt, pie graph, selectbox, searchbox)
- Modal for opening any Livewire component
- Quill editor

## Documentation

- [Installation](docs/installation.md)
- [Usage](docs/usage.md)
- [Configuration](docs/configuration.md)
- [Menu Builder](docs/menu.md)
- [Alerts](docs/alerts.md)
- [CRUD Generation](docs/crud.md)
- [Model Traits](docs/traits.md)
- [Commands](docs/commands.md)
- [Components](docs/components.md)
- [Development](docs/development.md)
- [Testing](docs/testing.md)

Upgrade guides:

- [v2.6 to v3.0](docs/updates/upgrade-3_0.md)
- [v2.3 to v2.4](docs/updates/upgrade-2_4.md)
- [v2.2 to v2.3](docs/updates/upgrade-2_3.md)

Import javascript and styles (includes bootstrap and font awesome)
```scss
// /resources/scss/app.scss
@import "./boilerplate/boilerplate.scss"
```
```js
// /resources/js/app.scss
import './boilerplate/boilerplate.js';
```
> [!NOTE]
> If you need customize any of included js/scss files, don't change files inside boilerplate folder.
> Instead create new root file boilerplate.scss/js by copying it from boilerplate folder. By changing paths of imported files you can make your custom verison or keep importing from boilerplate.
> When you update boilerplate package you will need to check changes only in root files boilerplate.scss/js and update your custom version accordingly.

## Menu Builder
### Single Level
```php
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

### with sub Menu  Builder
### Multi Level
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

## Alerts
### types
	success
	error
	warning
	info

### RELOAD type
```php
	Alert::add(type: 'info', text: 'Informační zpráva po redirektu', icon: '', mode: AlertModeType::RELOAD, persist: false);
```

### INSTANT type
```php
	Alert::add(type: 'error', text: 'Error zpráva ve stejném requestu', icon: '', mode: AlertModeType::INSTANT, persist: false);
```
### parametry
icon - využívá defaultní ikonu dle typu, pokud není nastavena
persist - pokud je true, zůstává notifikace aktivní dokud ji neodklikne uživatel nebo neprovede redirect.

## Commands

| Command | Description |
|---------|-------------|
| [install:boilerplate](.docs/commands/install-boilerplate.md) | Install or update boilerplate scaffolding into the project |
| [make:crud](.docs/commands/make-crud.md) | Scaffold a full CRUD resource (DataTable, Form, controller, routes, tests) |
| [make:basic-tests](.docs/commands/make-basic-tests.md) | Generate basic route coverage tests |
| [job:dispatch](.docs/commands/job-dispatch.md) | Dispatch a job by class name |

## Components

| Component | Tag | Description |
|-----------|-----|-------------|
| [Tab Group](.docs/components/tab.md) | `<x-boilerplate::tab.group>` | Bootstrap tabs with Alpine.js state and cookie persistence |
| [Breadcrumb](.docs/components/breadcrumb.md) | `<x-boilerplate::breadcrumb>` | Bootstrap breadcrumb from associative array |
| [Gantt](.docs/components/gantt.md) | `<x-boilerplate::gantt>` | Horizontal Gantt chart with day/week/month scale |
| [Pie Graph](.docs/components/pie-graph.md) | `<x-boilerplate::pie-graph>` | Chart.js pie chart |
| [Selectbox](.docs/components/selectbox.md) | `<x-boilerplate::selectbox>` | Alpine.js select with tags mode and multi-select |
| [Selectbox Ajax](.docs/components/selectbox.md#selectbox-ajax) | `<x-boilerplate::selectbox-ajax>` | Selectbox with server-side Livewire search |
| [Searchbox](.docs/components/searchbox.md) | `<x-boilerplate::searchbox>` | Client-side filtered dropdown search |
| [Alert](.docs/components/alert.md) | `alert()->success()->now()` | Snackbar notifications for Livewire and HTTP contexts |
| [Action Button](.docs/components/action-button.md) | `<x-boilerplate::action-button>` | Livewire button with scoped loading state, confirm and disabled reason tooltip |
| [Alert (inline)](.docs/components/alert-box.md) | `<x-boilerplate::alert>` | Inline alert with icon, title, actions and close button |
| [Icon Tile](.docs/components/icon-tile.md) | `<x-boilerplate::icon-tile>` | Square icon with a soft background in a theme color |
| [Stat](.docs/components/ui.md#stat) | `<x-boilerplate::stat>` | Number tile with icon |
| [Progress](.docs/components/ui.md#progress) | `<x-boilerplate::progress>` | Progress bar with label, percentage and indeterminate state |
| [Dropdown](.docs/components/ui.md#dropdown) | `<x-boilerplate::dropdown>` | Actions menu with icons, danger items and dividers |
| [Detail List](.docs/components/ui.md#detail-list) | `<x-boilerplate::detail-list>` | Label / value rows for detail pages |
| [Time](.docs/components/ui.md#time) | `<x-boilerplate::time>` | Relative time with exact date in a tooltip |
| [Copy](.docs/components/ui.md#copy) | `<x-boilerplate::copy>` | Copy to clipboard |
| [Empty State](.docs/components/ui.md#empty-state) | `<x-boilerplate::empty-state>` | Empty list placeholder |
| [Nav Switch](.docs/components/ui.md#nav-switch) | `<x-boilerplate::nav-switch>` | Segmented switch |
| [Ace editor](.docs/components/ace.md) | `<x-form::ace>` | Options, read-only mode, light/dark theme, npm loading |

## [Development](.docs/development.md)

## Contributors

<a href="https://github.com/steelants/Laravel-Boilerplate/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=steelants/Laravel-Boilerplate" />
</a>

## Other Packages

- [Laravel-Auth](https://github.com/steelants/Laravel-Auth)
- [Livewire-DataTable](https://github.com/steelants/Livewire-DataTable)
- [Laravel-Boilerplate.Warehouse](https://github.com/steelants/Laravel-Boilerplate.Warehouse)
- [Laravel-Boilerplate.Dashboard](https://github.com/steelants/Laravel-Boilerplate.Dashboard)
- [Laravel-Boilerplate.Automation](https://github.com/steelants/Laravel-Boilerplate.Automation)
- [Laravel-Boilerplate.Form](https://github.com/steelants/Laravel-Boilerplate.Form)
- [Laravel-Boilerplate.Calendar](https://github.com/steelants/Laravel-Boilerplate.Calendar)
- [Laravel-Boilerplate.CMS](https://github.com/steelants/Laravel-Boilerplate.CMS)
- [Laravel-Form](https://github.com/steelants/Laravel-Form)
- [Livewire-Form](https://github.com/steelants/Livewire-Form)
- [Laravel-General](https://github.com/steelants/Laravel-General)
- [Laravel-Tenant](https://github.com/steelants/Laravel-Tenant)

## License

This package is open-sourced software licensed under the [Apache License 2.0](LICENSE).

Copyright 2023-2026 SteelAnts s.r.o.
