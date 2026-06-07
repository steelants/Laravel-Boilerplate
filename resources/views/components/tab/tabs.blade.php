@props(['variant' => 'tabs'])

<ul {{ $attributes->class(['nav', 'nav-' . $variant, 'mb-3']) }}
    role="tablist"
    x-init="
        const key = $el.closest('[data-tab-remember]')?.dataset?.tabRemember;
        if (key) { $el.id = key; $el.classList.add('remember'); }
    "
>
    {{ $slot }}
</ul>
