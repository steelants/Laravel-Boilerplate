# make:basic-tests

Generates a `BasicCoverageTest.php` Pest test file that covers all registered GET routes (excluding auth, system, and parameter-based routes) with three test cases each: guest redirect, stranger 403, and unauthenticated redirect.

## Signature

```bash
php artisan make:basic-tests [--force]
```

## Options

| Option | Description |
|--------|-------------|
| `--force` | Overwrite existing `BasicCoverageTest.php` without prompting |

## What gets generated

File: `tests/Feature/BasicCoverageTest.php`

For each qualifying GET route, three test methods are generated:

| Test | Description |
|------|-------------|
| `test_guest_cant_access_{route}` | Guest user gets 302 redirect to `/login` |
| `test_stranger_cant_access_{route}` | `stranger` user gets 403 |
| `test_unauthorized_is_redirected_to_login_from_{route}` | Unauthenticated request gets 302 redirect to `/login` |

Routes are excluded if they contain `{` (parameters), `_`, `csrf`, `testing`, `system`, or belong to `App\Http\Controllers\Auth`.

## Requirements

The generated test extends `Tests\BaseTestTemplate`, which must define `$this->guest` and `$this->stranger` users.

## Example

```bash
php artisan make:basic-tests

# Force overwrite
php artisan make:basic-tests --force
```
