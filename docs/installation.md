# Installation

SteelAnts Laravel-Boilerplate is installed using Composer.


## Requirements

- Laravel 11 or 12
- PHP version compatible with your Laravel installation

The following SteelAnts packages are installed automatically as dependencies:

- steelants/laravel-auth
- steelants/datatable
- steelants/form
- steelants/livewire-form
- steelants/modal
- steelants/laravel-general


## Install the Package

Install the package using Composer:

```bash
composer require steelants/laravel-boilerplate
```

Laravel automatically discovers the service provider.


## Run the Installer

Install the boilerplate scaffolding:

```bash
php artisan install:boilerplate
```

The installer:

1. Copies controllers, views, assets and config into your application.
2. Appends the boilerplate routes to `routes/web.php`.
3. Updates `package.json` with the required JS dependencies and copies `vite.config.js`.
4. Runs the database migrations.
5. Clears caches and links the storage directory.

Files you have modified are only replaced after confirmation.

For all options and the update behavior see:

[install:boilerplate command](commands/install-boilerplate.md)


## Import Assets

Import the styles in `resources/scss/app.scss`:

```scss
@import "./boilerplate/boilerplate.scss";
```

Import the scripts in `resources/js/app.js`:

```js
import './boilerplate/boilerplate.js';
```

The imports include Bootstrap and Font Awesome.

> Do not change files inside the `boilerplate` folder.
> To customize, copy `boilerplate.scss` / `boilerplate.js` into a new root file and adjust the imported paths.
> After a package update you only need to compare the root files with your custom version.


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
