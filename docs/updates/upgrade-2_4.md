# Upgrade Guide

## v2.3 → v2.4

### Exception handling, middleware alias and cookie encryption moved to ServiceProvider

Previously, `install:boilerplate` injected code blocks into `bootstrap/app.php`:

```php
/* BOILERPLATE exceptions */ ... /* BOILERPLATE exceptions */
/* BOILERPLATE exceptionUses */ ... /* BOILERPLATE exceptionUses */
/* BOILERPLATE cookies */ ... /* BOILERPLATE cookies */
```

These are now registered automatically by `BoilerplateServiceProvider`. Running `install:boilerplate` will remove the old BOILERPLATE blocks automatically.

**Action required — only if you customized these blocks:**

If you removed the surrounding `/* BOILERPLATE ... */` comments to take ownership of the block, the automatic cleanup will not touch it. You need to manually remove or migrate the code:

- `exceptions` block → override `renderable()` in your own `AppServiceProvider`
- `cookies` block (`encryptCookies`) → the package registers `theme`, `layout-nav`, `toggleState`, `tabState` automatically; add any extra cookies via `EncryptCookies::except([...])` in your `AppServiceProvider`
- `cookies` block (`middleware alias`) → `is-system-admin` is now registered by the package; remove the duplicate from `bootstrap/app.php`

---

### Improved install manifest (`storage/boilerplate_install.json`)

The manifest format has changed to support per-file tracking and metadata.

**Old format:**
```json
{
  "/resources/views/system": "abc123..."
}
```

**New format:**
```json
{
  "_meta": {
    "installed_at": "2025-01-10T10:00:00+00:00",
    "updated_at": "2025-04-20T14:00:00+00:00"
  },
  "files": {
    "resources/views/system/users/index.blade.php": "abc123..."
  }
}
```

The old format is automatically migrated on the next `install:boilerplate` run. No manual action needed.

**New update behavior:**

| State | Action |
|---|---|
| File unchanged in both package and project | Skipped entirely |
| Package updated file, user did not modify | Auto-updated silently |
| User modified file | Prompted before overwrite |
| File not tracked (pre-2.4 install) | Prompted before overwrite |

**Two-phase install:**
Safe files are copied first. If any customized files are detected, you are prompted to commit your changes before proceeding.

---

### Fluent alert API

Alerts can now be dispatched using the `alert()` helper instead of calling `$this->dispatch('snackbar', [...])` directly in Livewire components.

**Before:**
```php
$this->dispatch('snackbar', ['message' => __('Saved'), 'type' => 'success', 'icon' => 'fas fa-check']);
```

**After:**
```php
alert()->success(__('Saved'))->now();
```

The helper works transparently in both Livewire and standard HTTP contexts:

- Inside a Livewire request (`X-Livewire` header) — alert is dispatched via `$this->component->dispatch('snackbar', ...)` through `AlertDispatcherHook`
- Inside a regular HTTP request — alert is stored in session and rendered by the `<x-boilerplate::alerts>` component

`Alert::add(AlertModeType::INSTANT)` also works automatically in Livewire context via the same hook.

**Available methods:**
```php
alert()->success('Saved!')->now();          // success, show immediately
alert()->error('Failed!')->reload();        // error, show after redirect
alert()->warning('Check input')->now();
alert()->info('Note')->now();
alert('success', 'Saved!')->now();          // shorthand with helper args
alert()->success('Saved!')->persist()->now(); // won't auto-close
```

---

### Removed stubs

The following stubs have been deleted as their functionality moved to the ServiceProvider:

- `stubs/exceptions.stub`
- `stubs/exceptionUses.stub`
- `stubs/cookies.stub`
