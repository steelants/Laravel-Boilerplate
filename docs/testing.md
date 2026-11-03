# Testing

SteelAnts Laravel-Boilerplate uses automated tests to verify package functionality.

The package uses:

- Pest
- Orchestra Testbench


## Install Dependencies

Install development dependencies:

```bash
composer install
```


## Running Tests

Run the complete test suite:

```bash
./vendor/bin/pest
```


## Running Specific Tests

You can run only selected tests by providing the test file:

```bash
./vendor/bin/pest tests/Feature/HasSettingsTraitTest.php
```


## Adding New Tests

When adding new functionality:

1. Create a test covering the new behavior.
2. Run the complete test suite.
3. Verify existing functionality is not affected.

Example test structure:

```
tests/
|-- Feature/
|   |-- HasSettingsTraitTest.php
|   `-- ...
|-- Unit/
|   `-- ...
`-- Pest.php
```


## Application Tests

CRUD resources generated with `make:crud --tests` include Pest feature tests for your application.

Basic route coverage tests can be generated using:

```bash
php artisan make:basic-tests
```


## Tests for Applications

Tests in `tests/App/` run in the package and are also meant to be copied into applications as
`tests/Feature/Boilerplate/`. The upgrade guide ([v3.1 to v3.2](updates/upgrade-3_2.md)) tells the
AI agent performing the upgrade to copy them. Write them so they work in both places:

- declare `uses(\Tests\TestCase::class)` - in the package it is an alias of the testbench test
  case (`tests/Pest.php`),
- keep helper classes in the test file under the `SteelAnts\LaravelBoilerplate\Tests\App`
  namespace, `tests/Fixtures` is not autoloaded in applications,
- do not depend on testbench-only setup (package migrations, routes from `TestCase`).

Run only these tests:

```bash
./vendor/bin/pest --testsuite=App
```


## Before Creating a Pull Request

Before submitting changes:

Run:

```bash
composer install
```

Then:

```bash
./vendor/bin/pest
```

Check the code style:

```bash
composer lint
```

All tests should pass before merging changes.


## Next Steps

Continue with:

- [Development](development.md)
- [Usage](usage.md)
- [CRUD Generation](crud.md)
