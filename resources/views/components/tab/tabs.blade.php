@props(['variant' => 'tabs', 'scroll' => false])

<ul {{ $attributes->class(['nav', 'nav-' . $variant, 'mb-3', 'flex-nowrap overflow-x-auto overflow-y-hidden text-nowrap' => $scroll]) }}
    role="tablist"
    x-init="
        const key = $el.closest('[data-tab-remember]')?.dataset?.tabRemember;
        if (key) { $el.id = key; $el.classList.add('remember'); }
        @if($scroll) scroll = true; @endif
    "
>
    {{ $slot }}
</ul>
