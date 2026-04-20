# Selectbox

**Tag:** `<x-boilerplate::selectbox>`

Alpine.js-powered select with optional multi-select tags mode, search, and pill display.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | required | `['id' => 'Label']` or `[['id' => ..., 'name' => ...]]` |
| `property` | string | `null` | Livewire `wire:model` property name |
| `selected` | mixed | `null` | Pre-selected value(s) |
| `label` | string | `null` | Field label rendered above the selectbox |
| `innerLabel` | string | `null` | Placeholder shown inside the control |
| `placeholder` | string | `null` | Placeholder for empty state |
| `multiple` | bool | `false` | Enables multi-select tags mode |
| `searchable` | string | `null` | Livewire method name for dynamic search (see [selectbox-ajax](selectbox-ajax.md)) |
| `pills` | int | `10` | Number of selected items shown as pills before collapsing |
| `required` | bool | `false` | Marks the field as required |

## Single select

```blade
<x-boilerplate::selectbox
    :options="$users"
    property="user_id"
    label="Assign to"
    placeholder="Select user..."
/>
```

## Multi-select with tags

```blade
<x-boilerplate::selectbox
    :options="$tags"
    property="tag_ids"
    label="Tags"
    multiple
/>
```

---

# Selectbox Ajax

**Tag:** `<x-boilerplate::selectbox-ajax>`

Extends Selectbox with server-side dynamic search via a Livewire method. Use when the options list exceeds ~100 items.

## Additional setup

Add the `SearchableSelectbox` trait to your Livewire component and expose a `#[Renderless]` method:

```php
use SearchableSelectbox;

#[Renderless]
public function getUsers($search = '')
{
    return $this->searchableSelectbox($search, User::class, $this->user_id)->toArray();
}
```

## Usage

```blade
<x-boilerplate::selectbox-ajax
    :options="$this->getUsers()"
    searchable="getUsers"
    property="user_id"
/>
```

Multi-select:

```blade
<x-boilerplate::selectbox-ajax
    :options="$this->getUsers()"
    searchable="getUsers"
    property="user_ids"
    multiple
/>
```
