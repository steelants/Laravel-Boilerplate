@props(['variant' => 'tabs', 'scroll' => false])

<ul {{ $attributes->class(['nav', 'nav-' . $variant, 'mb-3', 'nav-scroll' => $scroll]) }}
    role="tablist"
    x-init="
        const key = $el.closest('[data-tab-remember]')?.dataset?.tabRemember;
        if (key) { $el.id = key; $el.classList.add('remember'); }
    "
>
    {{ $slot }}
</ul>
