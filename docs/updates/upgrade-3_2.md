# Upgrade Guide

## v3.1 -> v3.2

### Modal `size` and `static` are locked, invalid values throw

The `boilerplate.modal.basic` properties `size` and `static` are now `#[Locked]`. The client can
no longer overwrite them; only the server-side `openModal` sets them.

`title` stays writable from the client, so `set-modal-title` keeps changing it without an extra
request. It is display text only; a non-string title sent by the client throws.

`openModal()` validates its arguments before it opens anything and throws
`InvalidArgumentException` with the type and the opened components in the message:

| Argument | Allowed |
|---|---|
| `title` | string or `null` (`null` becomes `''`) |
| `size` | `sm`, `md`, `lg`, `xl` |
| `static` | bool |

Previously an array `size` or `title` crashed the render with
`htmlspecialchars(): Argument #1 ($string) must be of type string, array given`. Values such as
`static: 0` or `size: 'xxl'` used to pass silently and now throw, so check your `openModal` calls
(PHP `dispatch` and JavaScript `Livewire.dispatch`). The arguments have no PHP types on purpose:
Livewire turns a `TypeError` into `419 Page expired` in production and the error is never
reported, while `InvalidArgumentException` goes through the exception handler.

**Action required** only if your JavaScript writes to the modal directly. Setting `$wire.size` or
`$wire.static` now throws `CannotUpdateLockedPropertyException`. The size can only be set when
opening the modal (`openModal(..., size: 'lg')`).

To change the title, use the event rather than `$wire.title`:

```js
Livewire.dispatch('set-modal-title', { title: 'New title' });
```

```php
$this->dispatch('set-modal-title', title: __('New title'));
```

The named `title:` form now works too; it used to be ignored because the listener read only the
first positional argument. The title is written as text (`textContent`, previously `innerHTML`), so
HTML in it is shown as text.
