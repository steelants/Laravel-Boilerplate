@php
    $classes = ['badge', 'badge-' . $size => $size != 'md'];

    if ($variant == 'subtle') {
        $classes = array_merge($classes, [
            'border',
            'border-'.$color.'-subtle',
            'bg-'.$color.'-subtle',
            'text-'.$color.'-emphasis',
        ]);
    } else {
        $classes = array_merge($classes, [
            'text-bg-'.$color,
        ]);
    }
@endphp

<span {{ $attributes->class($classes) }}
    class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle"
>
    @if ($icon)
        <i class="badge-icon {{ $icon }}"></i>
    @endif
    {{ $slot }}
</span>
