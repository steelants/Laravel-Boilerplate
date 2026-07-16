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
