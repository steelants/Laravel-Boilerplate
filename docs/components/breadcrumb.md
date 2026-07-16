# Breadcrumb

**Tag:** `<x-boilerplate::breadcrumb>`

Renders a Bootstrap breadcrumb navigation from an associative array of links.

## Props

| Prop | Type | Description |
|------|------|-------------|
| `items` | array | Associative array `['path' => 'Label']`. Paths are passed through `url()`. |

## Usage

```blade
<x-boilerplate::breadcrumb :items="[
    '/' => 'Home',
    '/system/users' => 'Users',
    '' => 'Edit',
]" />
```
