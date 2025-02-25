# Laravel-Boilerplate

## Currently WIP

### Created by: [SteelAnts s.r.o.](https://www.steelants.cz/)

[![Total Downloads](https://img.shields.io/packagist/dt/steelants/laravel-boilerplate.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-boilerplate)

## Content
- User Management
- Job Management
- Cache Management
- Backup Manager
- Log Viewer
- Audit

## Install

```bash
composer require steelants/laravel-boilerplate
composer install

#you can publish Migration for cutomization inside your project
php artisan vendor:publish --tag=boilerplate-migrations
php artisan migrate

#add Basic Controllers Routes and Features to APP namespace for customization
php artisan install:boilerplate
```

## Development

1. Create subfolder `/packages` at root of your laravel project

2. clone repository to sub folder `/packages` (you need to be positioned at root of your laravel project in your terminal)
```bash
git clone https://github.com/steelants/Laravel-Boilerplate.git ./packages/Laravel-Boilerplate
```

3. edit composer.json file
```json
"autoload": {
	"psr-4": {
		"SteelAnts\\LaravelBoilerplate\\": "packages/Laravel-Boilerplate/src/"
	}
}
```

4. Add provider to `bootstrap/providers.php`
```php
return [
	...
	SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider::class,
	...
];
```

5. use commands to aplicate changes
```bash
composer dump-autoload
```

6. aplicate packages changes - before this you need have auth package
```bash
php artisan install:boilerplate --force
```

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
