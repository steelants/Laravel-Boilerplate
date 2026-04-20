# Alert / Snackbar

Fluent helper for dispatching snackbar notifications. Works transparently in both Livewire and standard HTTP contexts — no need to call `$this->dispatch('snackbar', [...])` manually.

## Basic usage

```php
alert()->success('Saved!')->now();
alert()->error('Something went wrong')->now();
alert()->warning('Check your input')->now();
alert()->info('New version available')->now();
```

## Helper shorthand

The first two arguments of `alert()` set the type and message directly:

```php
alert('success', 'Saved!')->now();
alert('error', 'Something went wrong')->now();
```

## Timing — `now()` vs `reload()`

| Method | When shown | Use case |
|--------|-----------|----------|
| `->now()` | Immediately in the current request | Livewire actions, inline responses |
| `->reload()` | After the next page redirect | Controller actions that redirect |

```php
// Livewire component method
alert()->success('User created')->now();

// Controller that redirects
alert()->success('User created')->reload();
return redirect()->route('users.index');
```

## Custom icon

Default icons are set per type (`fas fa-check`, `fas fa-times`, etc.). Override with `->icon()` or pass as second argument:

```php
alert()->success('Deployed!', 'fas fa-rocket')->now();
alert()->success('Deployed!')->icon('fas fa-rocket')->now();
```

## Persist (do not auto-close)

```php
alert()->error('Action required')->persist()->now();
```

## HTTP context (Blade)

In a non-Livewire request, `->now()` stores the alert in session (`Session::now`). The Blade component renders it:

```blade
<x-boilerplate::alerts />
```

`->reload()` uses `Session::flash` — survives one redirect.

## Livewire context

In a Livewire request (`X-Livewire` header present), `->now()` queues the alert internally. After the action completes, `AlertDispatcherHook` picks it up and dispatches it via `$component->dispatch('snackbar', [...])`, which fires a browser `CustomEvent` caught by the global JS listener.

No extra setup is needed — the hook is registered automatically by `BoilerplateServiceProvider`.

## Low-level API

`AlertCollector` is available via the service container if you need direct access:

```php
use SteelAnts\LaravelBoilerplate\Support\AlertCollector;
use SteelAnts\LaravelBoilerplate\Types\AlertModeType;

app(AlertCollector::class)->add('success', 'Saved!', 'fas fa-check', AlertModeType::INSTANT);
```
