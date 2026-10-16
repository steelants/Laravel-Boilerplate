# Icon Tile

**Tag:** `<x-boilerplate::icon-tile>`

Square icon on a soft background tinted by `color`. Used in lists, cards, alerts and menu items; the [Empty State](ui.md#empty-state) is built on it.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `null` | Icon classes. When empty, the slot is rendered instead (initials, a number…) |
| `color` | string | `primary` | Bootstrap theme color (see below) |
| `size` | string | `md` | `sm`, `md`, `lg` |

## Colors

Any Bootstrap theme color (`primary`, `secondary`, `success`, `danger`, `warning`, `info`, `light`, `dark`). The tile uses the `bg-{color}-subtle` and `text-{color}-emphasis` utilities, so it follows light / dark mode.

## Usage

```blade
<x-boilerplate::icon-tile icon="fas fa-file-invoice" color="success" />
<x-boilerplate::icon-tile color="info" size="sm">JD</x-boilerplate::icon-tile>
```
