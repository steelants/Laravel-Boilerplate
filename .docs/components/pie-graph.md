# Pie Graph

**Tag:** `<x-boilerplate::pie-graph>`

Renders a Chart.js pie chart.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `data` | array | required | Associative array `['key' => value]` |
| `labels` | array | `[]` | Optional label overrides `['key' => 'Display label']`. Falls back to key name. |
| `colors` | array | `[]` | Optional color overrides `['key' => '#hex']`. Falls back to Chart.js defaults. |
| `legend` | bool | `true` | Show/hide the chart legend |

## Usage

```blade
<x-boilerplate::pie-graph
    :data="['active' => 42, 'inactive' => 18, 'pending' => 7]"
    :labels="['active' => 'Active users', 'inactive' => 'Inactive', 'pending' => 'Pending']"
    :colors="['active' => '#198754', 'inactive' => '#dc3545', 'pending' => '#ffc107']"
/>
```
