# Laravel-Boilerplate

#### Currently WIP

### Created by: [SteelAnts s.r.o.](https://www.steelants.cz/)

[![Total Downloads](https://img.shields.io/packagist/dt/steelants/laravel-boilerplate.svg?style=flat-square)](https://packagist.org/packages/steelants/laravel-boilerplate)

## Preview
[boilerplate.steelants.cz](https://boilerplate.steelants.cz)

## Change notes
### 2.3.0
Now by default modal will show only livewire that have AllowInModal attribute.

```php
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

// only logged in users
#[AllowInModal()]
class Form extends Component

#[AllowInModal(asGuest: true)]
class Form extends Component

// only for users with Gate::allows('is-admin')
#[AllowInModal(ability: 'is-admin')]
class Form extends Component
```

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

## CRUD
### Create CRUD
Create default files in model livewire
```bash
php artisan make:crud {model name}
```
### Create CRUD Forced
Create default files in model livewire with ovewrite
```bash
php artisan make:crud {model name} --force
```
### Create CRUD Full Page
Create default files in livewire with create and edit as full page
```bash
php artisan make:crud {model name} --full-page-components
```
### Create CRUD Full Page
Create components in custome namespace
```bash
php artisan make:crud {model name} --namespace=\\Admin
```

## CRUD parameters
### Add prefix in TestController
Give you for example "admin.test.datatable"
```php
public string $prefix = "admin.";
```

### Add model options in TestController
Give to add modal size parameter
```php
public function __construct()
{
	$this->model_component = [
		'size' => 'lg',
	];
}
```

## SelectboxAjax (WIP)
When you have selectbox with more than 100 options, it's recommended to use dynamic search with livewire (for now only available in selextbox-ajax).
Alpine will then call method speicified in searchagble parameter.
```html
<x-selectbox-ajax :options="$this->getOptions()" searchable="getOptions" property="user_ids" multiple/>
```
Only thing you need to change is to create in livewire component renderless function that will handle initial load and searching.
```php

	#[Renderless]
	public function getOptions($search = '')
	{
		if (!empty($search)) {
			// handle searching
			return User::select('name', 'id')->whereLike('name', '%'.$search.'%')
				->limit(30)
				->get()
				->toArray();
		} else {
			// handle default load
			return User::select('name', 'id')
				->whereIn('id', $this->user_ids) // always load selected value
				->union(
					// with union get 30 more users
					User::select('name', 'id')
					->whereNotIn('id', $this->user_ids)
					->limit(30)
				)
				->orderBy('id')
				->get();
		}
	}
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
