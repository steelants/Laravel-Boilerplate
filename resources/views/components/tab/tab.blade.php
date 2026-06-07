@props(['name', 'active' => false, 'disabled' => false])

<li data-tab-item class="nav-item" role="presentation">
    <button
        @if($active && !$disabled) x-init="setTab('{{ $name }}')" @endif
        :class="{ 'active': activeTab === '{{ $name }}' }"
        class="nav-link {{ $disabled ? 'disabled' : '' }}"
        @if(!$disabled) @click="setTab('{{ $name }}'); $el.dispatchEvent(new Event('shown.bs.tab', { bubbles: true }))" @endif
        type="button"
        role="tab"
        id="{{ $name }}"
        :aria-selected="activeTab === '{{ $name }}'"
        @if($disabled) disabled aria-disabled="true" @endif
    >{{ $slot }}</button>
</li>
