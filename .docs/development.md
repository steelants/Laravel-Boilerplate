# Development Environment Setup

How to set up a local development environment for contributing to or customizing this package.

## Steps

1. Create subfolder `/packages` at root of your Laravel project

2. Clone the repository into it (run from project root):
```bash
git clone https://github.com/steelants/Laravel-Boilerplate.git ./packages/Laravel-Boilerplate
```

3. Add the package to `composer.json` autoload:
```json
"autoload": {
    "psr-4": {
        "SteelAnts\\LaravelBoilerplate\\": "packages/Laravel-Boilerplate/src/"
    }
}
```

4. Register the service provider in `bootstrap/providers.php`:
```php
return [
    ...
    SteelAnts\LaravelBoilerplate\BoilerplateServiceProvider::class,
    ...
];
```

5. Apply autoload changes:
```bash
composer dump-autoload
```

6. Install boilerplate scaffolding (requires auth package):
```bash
php artisan install:boilerplate --force
```

## Tagging a release

```bash
git checkout main
git pull origin main
git pull origin dev
git tag 1.8.4
git push --tags
git checkout dev
```
