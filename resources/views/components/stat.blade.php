@props(['label' => null, 'value' => null, 'icon' => null, 'color' => null, 'href' => null])

<{{ $href ? 'a' : 'div' }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['stat', 'gap-3', 'text-reset text-decoration-none' => $href]) }}>
    @if ($icon)
        <div @class(['stat-ico', 'is-' . $color => $color])><i class="{{ $icon }}"></i></div>
    @endif
    <div>
        @if ($label)
            <div class="stat-name">{{ $label }}</div>
        @endif
        <div class="stat-value">{{ $value ?? $slot }}</div>
        @isset($description)
            <div class="small text-body-secondary mt-1">{{ $description }}</div>
        @endisset
    </div>
</{{ $href ? 'a' : 'div' }}>
