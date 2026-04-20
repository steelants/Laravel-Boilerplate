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

The `make:crud` command scaffolds a full CRUD resource: a controller, a Livewire DataTable, a Livewire Form, and optionally routes and tests.

### Basic usage
Generates a Form component based on `FormComponent` + `HasModel` from `steelants/livewire-form`. Fields and options are resolved automatically from model fillables and casts — no manual property declarations needed.
```bash
php artisan make:crud {model name}
```

### Advanced mode
Generates a fully customizable Form with individual public properties, a manual `mount()`, separate `store()` / `update()` methods, and a per-field Blade template. Use this when you need fine-grained control over the form.
```bash
php artisan make:crud {model name} --advanced
```

### Full page components
Creates form as a standalone full-page route instead of a modal.
```bash
php artisan make:crud {model name} --full-page-components
```

### Custom namespace
Places generated components under a sub-namespace (e.g. `App\Livewire\Admin\Post`).
```bash
php artisan make:crud {model name} --namespace=\\Admin
```

### With Pest tests
Generates a `tests/Feature/{Model}CrudTest.php` with tests for guest redirect, index access, create, update and delete.
```bash
php artisan make:crud {model name} --tests
```

### Overwrite existing files
```bash
php artisan make:crud {model name} --force
```

### Options summary
| Option | Description |
|--------|-------------|
| `--advanced` | Individual properties + custom blade (fully customizable) |
| `--full-page-components` | Form as a full-page route instead of modal |
| `--namespace=\\Admin` | Custom sub-namespace for generated components |
| `--tests` | Generate a Pest feature test file |
| `--force` | Overwrite existing files without confirmation |

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

## Tab Group

```blade
<x-boilerplate::tab.group default="profile" remember="myTabs" variant="tabs">
    <x-boilerplate::tab.tab name="profile">Profile</x-boilerplate::tab.tab>
    <x-boilerplate::tab.tab name="account">Account</x-boilerplate::tab.tab>
    <x-boilerplate::tab.tab name="billing" :disabled="true">Billing</x-boilerplate::tab.tab>

    <x-boilerplate::tab.panel name="profile">Profile content</x-boilerplate::tab.panel>
    <x-boilerplate::tab.panel name="account">Account content</x-boilerplate::tab.panel>
    <x-boilerplate::tab.panel name="billing">Billing content</x-boilerplate::tab.panel>
</x-boilerplate::tab.group>
```

## SelectboxAjax (WIP)
When you have selectbox with more than 100 options, it's recommended to use dynamic search with livewire (for now only available in selextbox-ajax).
Alpine will then call method speicified in searchagble parameter.
```html
<x-selectbox-ajax :options="$this->getOptions()" searchable="getOptions" property="user_ids" multiple/>
```
Only thing you need to change is to create renderless function and call searchableSelectbox method that will handle everything.
```php
	use SearchableSelectbox;

	#[Renderless]
	public function getOptions($search = '')
	{
		return $this->searchableSelectbox($search, User::class, $this->user_id)->toArray();
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
