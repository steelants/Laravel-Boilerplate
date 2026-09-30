# Tab Group

**Tag:** `<x-boilerplate::tab.group>`

Renders a Bootstrap-styled tab navigation with Alpine.js state. Tabs are automatically moved into a `<ul class="nav">` element prepended to the group.

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `default` | string | `null` | Name of the tab active on first render |
| `remember` | string | `null` | Unique key — persists active tab in a cookie across page loads |
| `variant` | string | `tabs` | Bootstrap nav variant: `tabs`, `pills`, `underline` |
| `query` | string | `null` | Query string parameter that stores the active tab (`?tab=security`); read on render and updated on change |
| `scroll` | bool | `false` | Tabs scroll horizontally instead of wrapping (the active underline stays visible) |
| `wire:model` | string | — | Binds the active tab to a Livewire property (`wire:model.live` sends every change right away) |

### `<x-boilerplate::tab.tab>`

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | required | Unique identifier for this tab |
| `disabled` | bool | `false` | Disables the tab button |
| `badge` | string\|slot | `null` | Badge after the label (count, "new"…) |
| `badgeColor` | string | `secondary` | Badge color |

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

## In a Livewire component

Livewire re-renders the component's HTML, so wrap the tabs in `<x-boilerplate::tab.tabs>`. The tab list is then rendered on the server, not moved into place by JavaScript. Bind the active tab to a property; add `#[Url]` to the property to keep it in the URL too.

```php
use Livewire\Attributes\Url;

#[Url]
public string $tab = 'overview';
```

```blade
<x-boilerplate::tab.group wire:model.live="tab">
    <x-boilerplate::tab.tabs scroll>
        <x-boilerplate::tab.tab name="overview">{{ __('Overview') }}</x-boilerplate::tab.tab>
        <x-boilerplate::tab.tab name="comments" :badge="$commentsCount">{{ __('Comments') }}</x-boilerplate::tab.tab>
    </x-boilerplate::tab.tabs>

    <x-boilerplate::tab.panel name="overview">…</x-boilerplate::tab.panel>
    <x-boilerplate::tab.panel name="comments">
        @if ($tab === 'comments') {{-- lazy content --}} @endif
    </x-boilerplate::tab.panel>
</x-boilerplate::tab.group>
```

Outside Livewire, use `query="tab"` to keep the active tab in the URL.
