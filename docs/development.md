# Development

This guide describes how to develop SteelAnts Laravel-Boilerplate locally inside a Laravel application.


## Local Setup

Create a packages directory and clone the repository:

```bash
mkdir packages
git clone https://github.com/steelants/Laravel-Boilerplate.git ./packages/Laravel-Boilerplate
```

Update the autoload section of your application `composer.json`:

```json
"autoload": {
    "psr-4": {
        "SteelAnts\\LaravelBoilerplate\\": "packages/Laravel-Boilerplate/src/"
    }
}
```

Refresh the autoloader:

```bash
composer dump-autoload
```

Register the service provider in `bootstrap/providers.php`:

```php
return [
    // ...
    SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider::class,
];
```

Apply the package scaffolding (requires the steelants/laravel-auth package):

```bash
php artisan install:boilerplate --force
```


## Development Workflow

1. Create a feature branch.
2. Implement changes.
3. Add or update tests.
4. Run the test suite.
5. Merge changes into the development branch.

Before running the test suite see:

[Testing documentation](testing.md)


## Code Style

The package uses PHP_CodeSniffer with the Slevomat coding standard.

Check the code style:

```bash
composer lint
```

Fix the code style automatically:

```bash
composer format
```

Run static analysis:

```bash
composer check-static
```


## Tagging a Release

```bash
git checkout main
git pull origin main
git pull origin dev
git tag 1.8.4
git push --tags
git checkout dev
```


## Next Steps

Continue with:

- [Usage](usage.md)
- [Configuration](configuration.md)
- [Testing](testing.md)
