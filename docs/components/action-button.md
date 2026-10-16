# Action Button

**Tag:** `<x-boilerplate::action-button>`

Livewire button that handles its own loading state: while its action runs it shows a spinner instead of the icon and is disabled. Only its own `wire:target` triggers this, so other buttons on the page are not affected.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `action` | string | `null` | Livewire action, rendered as `wire:click` (`save`, `remove(5)`). You can pass `wire:click` directly instead |
| `target` | string | `action` | `wire:target` for the loading state |
| `confirm` | string | `null` | Confirmation text (`wire:confirm`) |
| `icon` | string | `null` | Icon classes, replaced by a spinner while loading |
| `variant` | string | `primary` | Bootstrap button variant (`danger`, `outline-secondary`…) |
| `size` | string | `null` | `sm` / `lg` |
| `type` | string | `button` | Button type |
| `disabled` | bool | `false` | Disables the button |
| `disabledReason` | string | `null` | Tooltip shown when the button is disabled |
| `tooltip` | string | `null` | Tooltip shown when the button is enabled (and when disabled without a reason) |

## Usage

```blade
<x-boilerplate::action-button action="save" icon="fas fa-save">{{ __('Save') }}</x-boilerplate::action-button>

<x-boilerplate::action-button
    action="remove({{ $post->id }})"
    icon="fas fa-trash"
    variant="danger"
    size="sm"
    :confirm="__('Are you sure?')"
/>

<x-boilerplate::action-button
    action="send"
    icon="fas fa-paper-plane"
    :disabled="!$recipients"
    :disabled-reason="__('Add at least one recipient')"
>{{ __('Send') }}</x-boilerplate::action-button>
```

> [!NOTE]
> A disabled button does not receive mouse events, so it is wrapped in a focusable `<span>` that carries the tooltip.
