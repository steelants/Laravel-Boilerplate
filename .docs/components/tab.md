# Tab Group

**Tag:** `<x-boilerplate::tab.group>`

Renders a Bootstrap-styled tab navigation with Alpine.js state. Tabs are automatically moved into a `<ul class="nav">` element prepended to the group.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `default` | string | `null` | Name of the tab active on first render |
| `remember` | string | `null` | Unique key — persists active tab in a cookie across page loads |
| `variant` | string | `tabs` | Bootstrap nav variant: `tabs`, `pills`, `underline` |

### `<x-boilerplate::tab.tab>`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Unique identifier for this tab |
| `disabled` | bool | `false` | Disables the tab button |

### `<x-boilerplate::tab.panel>`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Must match the corresponding tab `name` |

## Basic usage

```blade
<x-boilerplate::tab.group default="profile">
    <x-boilerplate::tab.tab name="profile">Profile</x-boilerplate::tab.tab>
    <x-boilerplate::tab.tab name="security">Security</x-boilerplate::tab.tab>

    <x-boilerplate::tab.panel name="profile">Profile content</x-boilerplate::tab.panel>
    <x-boilerplate::tab.panel name="security">Security content</x-boilerplate::tab.panel>
</x-boilerplate::tab.group>
```

## With remembered state and pills variant

```blade
<x-boilerplate::tab.group default="profile" remember="settingsTabs" variant="pills">
    <x-boilerplate::tab.tab name="profile">Profile</x-boilerplate::tab.tab>
    <x-boilerplate::tab.tab name="billing" :disabled="true">Billing</x-boilerplate::tab.tab>

    <x-boilerplate::tab.panel name="profile">Profile content</x-boilerplate::tab.panel>
    <x-boilerplate::tab.panel name="billing">Billing content</x-boilerplate::tab.panel>
</x-boilerplate::tab.group>
```
