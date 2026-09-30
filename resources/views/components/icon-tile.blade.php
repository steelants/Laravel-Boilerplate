@props(['icon' => null, 'color' => 'primary', 'size' => 'md'])

<span {{ $attributes->class(['icon-tile', 'icon-tile-' . $color, 'icon-tile-' . $size]) }} aria-hidden="true">
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @else
        {{ $slot }}
    @endif
</span>
