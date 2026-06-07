@props(['default' => null, 'remember' => null, 'variant' => 'tabs'])

@php
    $initialTab = $remember ? (getTabState($remember) ?: $default) : $default;
@endphp

<div x-data="{
    activeTab: @js($initialTab),
    setTab(name) { this.activeTab = name; }
}"
x-init="
    const ul = document.createElement('ul');
    ul.className = 'nav nav-{{ $variant }} mb-3';
    ul.setAttribute('role', 'tablist');
    @if($remember)
    ul.id = '{{ $remember }}';
    ul.classList.add('remember');
    @endif
    $el.querySelectorAll('[data-tab-item]').forEach(el => ul.appendChild(el));
    $el.prepend(ul);
"
{{ $attributes }}>
    {{ $slot }}
</div>
