# Upgrade Guide

## v2.6 -> v2.7

### Modal moved from `steelants/modal` into the boilerplate

The separate `steelants/modal` package is deprecated and archived. The modal is now part of
Laravel-Boilerplate and `steelants/modal` was removed from the package dependencies.

**Action required:**

1. Remove the package from your application `composer.json` if you required it explicitly:

   ```bash
   composer remove steelants/modal
   ```

2. Remove `SteelAnts\Modal\ModalServiceProvider::class` from `bootstrap/providers.php` if it was
   registered manually. Nothing has to be registered instead - `BoilerplateServiceProvider` takes
   care of the modal.

3. Replace the `AllowInModal` import in your Livewire components:

   ```php
   - use SteelAnts\Modal\Livewire\Attributes\AllowInModal;
   + use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;
   ```

   The old import keeps working as long as `steelants/modal` is installed, but it is checked only
   as a fallback and will be removed in a future major version.

4. Optionally rename the component in your layout:

   ```blade
   - @livewire('modal-basic', key('modal'))
   + @livewire('boilerplate.modal.basic', key('modal'))
   ```

   The legacy name `modal-basic` stays registered, so this step is not required.

### `AllowInModal` ability has to be a named argument

`#[AllowInModal('is-system-admin')]` passed the string into the first parameter `bool $asGuest`.
PHP coerced it to `true`, so the gate was never evaluated and the component could be opened by a
guest. Use the named argument:

```php
- #[AllowInModal('is-system-admin')]
+ #[AllowInModal(ability: 'is-system-admin')]
```

The boilerplate normalizes the old positional form to `ability` on its own, so components that were
unintentionally open to guests are now gated. Check that the ability you declared really is the one
you want enforced.

The view namespace changed from `modal::modal-basic` to `boilerplate::livewire.modal.basic`. If you
published or overrode the modal view, move it to `resources/views/vendor/boilerplate/livewire/modal/basic.blade.php`.

---
