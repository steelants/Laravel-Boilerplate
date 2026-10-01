# UI components

Small general-purpose components. They all accept extra attributes (`class`, `wire:*`…), which are merged onto the root element.

## Stat

**Tag:** `<x-boilerplate::stat>`

Tile with a number, built on `.stat` / `.stat-ico`.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | `null` | Caption above the value |
| `value` | string | slot | Value |
| `icon` | string | `null` | Icon classes |
| `color` | string | `null` | Icon background from the existing `.stat-ico` modifiers: `green`, `red`, `purple` (default dark) |
| `href` | string | `null` | Makes the whole tile a link |

Slot `description` renders a small line below the value.

```blade
<x-boilerplate::stat :label="__('Orders')" :value="$ordersCount" icon="fas fa-shopping-cart" color="green">
    <x-slot:description>+12 % {{ __('this month') }}</x-slot:description>
</x-boilerplate::stat>
```

## Progress

**Tag:** `<x-boilerplate::progress>`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `value` | number\|null | `null` | Current value; `null` renders the indeterminate state (full animated striped bar) |
| `max` | number | `100` | Maximum |
| `label` | string | `null` | Caption on the left |
| `percent` | bool | `true` | Shows the percentage on the right |
| `color` | string | `primary` | Bootstrap theme color |
| `height` | string | `null` | Bar height (`.5rem`) |

```blade
<div wire:poll.2s>
    <x-boilerplate::progress :label="__('Import')" :value="$job->processed" :max="$job->total" />
</div>
```

## Dropdown

**Tags:** `<x-boilerplate::dropdown>`, `<x-boilerplate::dropdown-item>`, `<x-boilerplate::dropdown-divider>`

| `dropdown` prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `fas fa-ellipsis-v` | Trigger icon |
| `label` | string | `null` | Trigger text |
| `variant` | string | `light` | Trigger button variant |
| `size` | string | `sm` | Trigger button size |
| `align` | string | `end` | Menu alignment (`start`, `end`) |
| `caret` | bool | `false` | Shows the dropdown caret |

Slot `trigger` replaces the default button.

| `dropdown-item` prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | `null` | Renders a link; otherwise a `<button>` (use `wire:click`) |
| `icon` | string | `null` | Icon classes |
| `danger` | bool | `false` | Red destructive item |
| `active` / `disabled` | bool | `false` | State |

```blade
<x-boilerplate::dropdown>
    <x-boilerplate::dropdown-item :href="route('post.edit', $post)" icon="fas fa-pen">{{ __('Edit') }}</x-boilerplate::dropdown-item>
    <x-boilerplate::dropdown-divider />
    <x-boilerplate::dropdown-item wire:click="remove({{ $post->id }})" wire:confirm="{{ __('Are you sure?') }}" icon="fas fa-trash" danger>
        {{ __('Delete') }}
    </x-boilerplate::dropdown-item>
</x-boilerplate::dropdown>
```

## Detail List

**Tags:** `<x-boilerplate::detail-list>`, `<x-boilerplate::detail-list.item>`

Label / value rows with dividers for detail pages. Stacked on mobile, side by side from `sm` up. An empty value renders `—`.

```blade
<x-boilerplate::detail-list :items="[__('Name') => $user->name, __('E-mail') => $user->email]">
    <x-boilerplate::detail-list.item :label="__('Created')">
        <x-boilerplate::time :datetime="$user->created_at" />
    </x-boilerplate::detail-list.item>
</x-boilerplate::detail-list>
```

## Time

**Tag:** `<x-boilerplate::time>`

Relative time ("2 hours ago") with the exact date in a tooltip.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `datetime` | DateTimeInterface\|string\|null | `null` | Date; `null` renders `—` (or the slot) |
| `format` | string | `LLL` | `isoFormat()` format of the exact date |
| `relative` | bool | `true` | `false` shows the exact date without a tooltip |

```blade
<x-boilerplate::time :datetime="$post->updated_at" />
```

## Copy

**Tag:** `<x-boilerplate::copy>`

Copy-to-clipboard button. The slot, if given, is shown before the button.

```blade
<x-boilerplate::copy :value="$token->plainTextToken">
    <code>{{ Str::mask($token->plainTextToken, '*', 6) }}</code>
</x-boilerplate::copy>
```

## Empty State

**Tag:** `<x-boilerplate::empty-state>`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `fas fa-inbox` | Icon in an [Icon Tile](icon-tile.md) |
| `color` | string | `secondary` | Tile color |
| `title` | string | `null` | Title |
| `description` | string | `null` | Text below the title |

The slot is rendered as actions.

```blade
<x-boilerplate::empty-state :title="__('No projects yet')" :description="__('Create your first project to get started.')">
    <a href="{{ route('project.create') }}" class="btn btn-primary">{{ __('Create project') }}</a>
</x-boilerplate::empty-state>
```

## Nav Switch

**Tag:** `<x-boilerplate::nav-switch>`

Segmented switch. With `wire:model` (or `name`) it renders radio buttons; options with `href` render as links.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `options` | array | `[]` | `value => label` or `value => ['label' => …, 'icon' => …, 'href' => …]` |
| `value` | string | `null` | Selected value (not needed with `wire:model`) |
| `name` | string | model name | Radio `name` |
| `size` | string | `sm` | Button group size |

```blade
<x-boilerplate::nav-switch wire:model.live="view" :options="[
    'list' => ['label' => __('List'), 'icon' => 'fas fa-list'],
    'grid' => ['label' => __('Grid'), 'icon' => 'fas fa-th'],
]" />
```
