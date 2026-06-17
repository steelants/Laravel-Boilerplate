# Searchbox

**Tag:** `<x-boilerplate::searchbox>`

Dropdown search input that filters a static options list client-side using Alpine.js.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | required | Associative array `['id' => 'Label']` |
| `property` | string | required | Livewire `wire:model` property name |
| `label` | string | `''` | Field label |
| `autoclose` | string | `outside` | When to close the dropdown: `outside`, `always`, `never` |

## Usage

```blade
<x-boilerplate::searchbox
    :options="$countries"
    property="country_id"
    label="Country"
/>
```
