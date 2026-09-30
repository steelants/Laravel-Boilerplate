# Icon Tile

**Tag:** `<x-boilerplate::icon-tile>`

Square icon on a soft background tinted by `color`. Used in lists, cards, alerts and menu items; the [Empty State](ui.md#empty-state) is built on it.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `icon` | string | `null` | Icon classes. When empty, the slot is rendered instead (initials, a number…) |
| `color` | string | `primary` | Any color from the palette (see below) |
| `size` | string | `md` | `sm`, `md`, `lg` |

## Palette

Theme colors `primary`, `secondary`, `success`, `danger`, `warning`, `info` and Tailwind hues `slate`, `gray`, `red`, `orange`, `amber`, `yellow`, `lime`, `green`, `emerald`, `teal`, `cyan`, `sky`, `blue`, `indigo`, `violet`, `purple`, `fuchsia`, `pink`, `rose`.

The palette is the `$boilerplate-palette` SCSS map (`_palette.scss`). It is also used by [Stat](ui.md#stat), and you can override it with `!default`.

## Usage

```blade
<x-boilerplate::icon-tile icon="fas fa-file-invoice" color="teal" />
<x-boilerplate::icon-tile color="violet" size="sm">JD</x-boilerplate::icon-tile>
```
