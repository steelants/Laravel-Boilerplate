@props(['default' => null, 'remember' => null, 'variant' => 'tabs', 'query' => null, 'scroll' => false])

@php
    $wireModel = $attributes->wire('model');
    $initialTab = $remember ? (getTabState($remember) ?: $default) : $default;
    if ($query && is_string(request()->query($query))) {
        $initialTab = request()->query($query);
    }
@endphp

<div x-data="{
    activeTab: {{ $wireModel->value() ? '$wire.entangle(' . \Illuminate\Support\Js::from($wireModel->value()) . ')' . ($wireModel->hasModifier('live') ? '.live' : '') : \Illuminate\Support\Js::from($initialTab) }},
    init() {
        if (!this.activeTab) { this.activeTab = @js($initialTab); }
        @if($query)
        this.$watch('activeTab', value => {
            const url = new URL(window.location.href);
            url.searchParams.set(@js($query), value);
            history.replaceState(history.state, '', url);
        });
        @endif
    },
    scroll: @js((bool) $scroll),
    setTab(name) { this.activeTab = name; }
}"
x-init="
    const items = [...$el.querySelectorAll('[data-tab-item]')].filter(el => !el.closest('[role=tablist]'));
    if (items.length) {
        const ul = document.createElement('ul');
        ul.className = 'nav nav-{{ $variant }} mb-3{{ $scroll ? ' flex-nowrap overflow-x-auto overflow-y-hidden text-nowrap' : '' }}';
        ul.setAttribute('role', 'tablist');
        @if($remember)
        ul.id = '{{ $remember }}';
        ul.classList.add('remember');
        @endif
        items.forEach(el => ul.appendChild(el));
        $el.prepend(ul);
    }
"
@if($remember) data-tab-remember="{{ $remember }}" @endif
{{ $attributes->whereDoesntStartWith('wire:model') }}>
    {{ $slot }}
</div>
