# Laravel-Boilerplate

### Created by: [SteelAnts s.r.o.](https://www.steelants.cz/)

[![Total Downloads](https://img.shields.io/packagist/dt/steelants/laravel-boilerplate.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-boilerplate)

## Preview
[boilerplate.steelants.cz](https://boilerplate.steelants.cz)

## Upgrade Guide
- [v2.3 → v2.4](.docs/upgrade/upgrade-2_5.md)
- [v2.2 → v2.3](.docs/upgrade/upgrade-2_3.md)

#### Tag project
```bash
  git checkout main
  git pull origin main
  git pull origin dev
  git tag 1.8.4
  git push --tags
  git checkout dev
```

## What's included
### Functions
- User Management
- Job Management
- Cache Management
- Backup Manager
- Log Viewer
- Audit
- API Routes view page
- Menu builder
- From builder
- Datatable builder

### Template
- Reponsive app template
- Light/dark theme
- Build on Bootstrap and Livewire
- Quill editor

## Install

```bash
composer require steelants/laravel-boilerplate
composer install

#add Basic Controllers Routes and Features to APP namespace for customization
php artisan install:boilerplate
```

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

## [Development](.docs/development.md)

## Contributors
<a href="https://github.com/steelants/Laravel-Boilerplate/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=steelants/Laravel-Boilerplate" />
</a>

## Other Packages
[steelants/laravel-auth](https://github.com/steelants/laravel-auth)

[steelants/laravel-boilerplate](https://github.com/steelants/Laravel-Boilerplate)

[steelants/datatable](https://github.com/steelants/Livewire-DataTable)

[steelants/form](https://github.com/steelants/Laravel-Form)

[steelants/modal](https://github.com/steelants/Livewire-Modal)

[steelants/laravel-tenant](https://github.com/steelants/Laravel-Tenant)


## Notes
* [Laravel MFA](https://dev.to/roxie/how-to-add-google-s-two-factor-authentication-to-a-laravel-8-application-4jjp)
