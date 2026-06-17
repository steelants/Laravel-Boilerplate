# Gantt

**Tag:** `<x-boilerplate::gantt>`

Renders a horizontal Gantt chart with configurable scale and row items.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `dateFrom` | string | required | Start date of the visible range (parsed by Carbon) |
| `dateTo` | string | required | End date of the visible range |
| `scale` | string | `month` | Time scale: `day`, `week`, `month` |
| `rows` | array | `[]` | Array of row definitions (see below) |

## Row structure

```php
[
    'title' => 'Row label',
    'items' => [
        [
            'title'    => 'Item label',
            'dateFrom' => '2025-01-10',
            'dateTo'   => '2025-01-20',
            'color'    => 'primary',   // Bootstrap color, default: secondary
            'progress' => 60,          // optional, 0–100
        ],
    ],
]
```

## Usage

```blade
<x-boilerplate::gantt
    dateFrom="2025-01-01"
    dateTo="2025-03-31"
    scale="month"
    :rows="[
        [
            'title' => 'Development',
            'items' => [
                ['title' => 'Phase 1', 'dateFrom' => '2025-01-05', 'dateTo' => '2025-01-25', 'color' => 'primary', 'progress' => 80],
                ['title' => 'Phase 2', 'dateFrom' => '2025-02-01', 'dateTo' => '2025-03-15', 'color' => 'success'],
            ],
        ],
    ]"
/>
```
