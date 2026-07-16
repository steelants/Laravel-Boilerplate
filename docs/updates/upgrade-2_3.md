# Upgrade Guide: v2.2 → v2.3

### Modal now requires `AllowInModal` attribute

By default modal will show only Livewire components that have the `AllowInModal` attribute.

```php
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

// only logged in users
#[AllowInModal()]
class Form extends Component

#[AllowInModal(asGuest: true)]
class Form extends Component

// only for users with Gate::allows('is-admin')
#[AllowInModal(ability: 'is-admin')]
class Form extends Component
```
