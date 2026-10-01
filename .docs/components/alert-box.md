# Alert (inline)

**Tag:** `<x-boilerplate::alert>`

Inline alert with an icon, title, text, actions on the right and an optional close button. For snackbar notifications see [Alert](alert.md).

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `info` | `success`, `info`, `warning`, `danger` (`error` is an alias for `danger`), or any Bootstrap theme color |
| `title` | string | `null` | Bold title |
| `icon` | string\|false | by type | Icon classes; pass `false` to hide the icon |
| `dismissible` | bool | `false` | Shows a close button |

### Slots

| Slot | Description |
|------|-------------|
| default | Alert text |
| `actions` | Buttons or links aligned top right |

## Usage

```blade
<x-boilerplate::alert type="warning" :title="__('Subscription expires soon')" dismissible>
    {{ __('Renew it before :date to keep access.', ['date' => $date]) }}

    <x-slot:actions>
        <a href="{{ route('billing') }}" class="btn btn-sm btn-warning">{{ __('Renew') }}</a>
    </x-slot:actions>
</x-boilerplate::alert>
```
