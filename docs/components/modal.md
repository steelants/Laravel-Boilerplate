# Modal

SteelAnts Laravel-Boilerplate opens any Livewire component inside a Bootstrap modal.

The modal was originally distributed as the separate `steelants/modal` package. It is now part of
Laravel-Boilerplate; see [v2.6 to v3.0](../updates/upgrade-3_0.md) for migration details.

The modal is controlled by the `openModal` and `closeModal` Livewire events.


## Requirements

- Livewire 3
- Bootstrap 5 (including the Bootstrap JavaScript bundle)


## Include the Modal

Add the Livewire component to your layout once:

```blade
@livewire('boilerplate.modal.basic', key('modal'))
```

The component renders the Bootstrap modal markup and listens for the open and close events.

> The legacy component name `modal-basic` is still registered, so layouts from older projects
> keep working without changes.


## Allow a Component in the Modal

A component is only opened when it is marked with the `AllowInModal` attribute:

```php
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;

#[AllowInModal(ability: 'is-system-admin')]
class UserForm extends Component
{
}
```

| Argument | Default | Description |
|---|---|---|
| `asGuest` | `false` | Allow the component to be opened by a guest |
| `ability` | `null` | Gate ability checked before the component is opened |
| `arguments` | `null` | Arguments passed to the gate check |

Without the attribute the modal stays closed. The check runs both when the modal is opened and on
every render, so a component can never be injected from the client side.

> Always pass the ability as a named argument. The older positional form `#[AllowInModal('my-ability')]`
> put the string into `asGuest`, so the gate was never evaluated and the component was open to
> guests. Such a declaration is now normalized to `ability` instead.


## Opening from Blade or JavaScript

```html
<button onclick="Livewire.dispatch('openModal', {livewireComponents: 'user-form', title: 'Create user'})">
    Create User
</button>
```

With component parameters:

```html
<button onclick="Livewire.dispatch('openModal', {livewireComponents: 'user-form', title: 'Edit user', parameters: {user: 2}})">
    Edit User
</button>
```


## Opening from a Livewire Component

```php
$this->dispatch(
    'openModal',
    livewireComponents: 'user-form',
    title: __('Edit user'),
    parameters: ['user' => $userId],
);
```


## Parameters

| Parameter | Default | Description |
|---|---|---|
| `livewireComponents` | - | Component name; can be an array of names |
| `title` | `""` | Modal title |
| `parameters` | `[]` | Parameters passed to the component `mount()` |
| `size` | `md` | Modal size (`sm`, `md`, `lg`, `xl`) |
| `static` | `true` | Static backdrop; the modal cannot be closed by clicking outside or pressing escape |


## Multiple Components

You can open several components in one modal:

```php
$this->dispatch(
    'openModal',
    livewireComponents: ['user-form', 'user-limitation'],
    title: __('User'),
);
```

The components are rendered in the given order. Every one of them has to be marked with
`AllowInModal`.


## Closing the Modal

Close the modal from any Livewire component:

```php
$this->dispatch('closeModal');
```

Closing the modal resets the opened components, parameters and title.


## Changing the Title

Update the title of an open modal:

```php
$this->dispatch('set-modal-title', title: __('New title'));
```


## Next Steps

Continue with:

- [Components](../components.md)
- [Usage](../usage.md)
