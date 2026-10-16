# Upgrade Guide

## v2.6 -> v3.0

### Modal moved from `steelants/modal` into the boilerplate

The separate `steelants/modal` package is deprecated and archived. The modal is now part of
Laravel-Boilerplate and `steelants/modal` was removed from the package dependencies.

Laravel-Boilerplate 3.0 declares a `conflict` with `steelants/modal`, so Composer refuses to
install them side by side. That is deliberate - both packages register the Livewire component
`modal-basic` and whichever service provider boots last wins. If the old one wins, components
marked with the new `AllowInModal` attribute are refused and the modal silently stays closed.

**Action required:**

1. Remove the package from your application:

   ```bash
   composer remove steelants/modal
   ```

2. Upgrade every SteelAnts package that still requires it. All `steelants/laravel-boilerplate.*`
   addons are released as 3.x alongside this version:

   ```bash
   composer update steelants/*
   ```

3. Remove `SteelAnts\Modal\ModalServiceProvider::class` from `bootstrap/providers.php` if it was
   registered manually. Nothing has to be registered instead - `BoilerplateServiceProvider` takes
   care of the modal.

4. Replace the `AllowInModal` import in every Livewire component you open in a modal:

   ```php
   - use SteelAnts\Modal\Livewire\Attributes\AllowInModal;
   + use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;
   ```

   **This step is mandatory.** The old attribute is no longer recognised. A component that keeps
   the old import is simply not allowed in the modal - nothing throws, the modal stays closed.
   Search your project for `SteelAnts\Modal` before upgrading.

5. Optionally rename the component in your layout:

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

### Modal view namespace changed

The view moved from `modal::modal-basic` to `boilerplate::livewire.modal.basic`. If you published
or overrode the modal view, move it to
`resources/views/vendor/boilerplate/livewire/modal/basic.blade.php`.

---

### `DatatableComponent` components require some locked properties

Component properties $sortableColumns, $searchableColumns and $viewName must be decorade with #[Locked]
