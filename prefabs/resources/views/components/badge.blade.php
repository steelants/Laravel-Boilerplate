@use('SteelAnts\LaravelBoilerplate\Support\BootstrapTheme')
@php
    $classes = ['badge', 'badge-' . $size => $size != 'md'];
    $theme = BootstrapTheme::theme($color);

    if ($variant == 'subtle') {
        $classes = array_merge($classes, [
            'border',
            'border-subtle-'.$theme,
            'bg-subtle-'.$theme,
            'fg-emphasis-'.$theme,
        ]);
    } else {
        $classes = array_merge($classes, [
            'theme-'.$theme,
        ]);
    }
@endphp

<span {{ $attributes->class($classes) }}
    class="badge bg-subtle-primary fg-emphasis-primary border border-subtle-primary"
>
    @if ($icon)
        <i class="badge-icon {{ $icon }}"></i>
    @endif
    {{ $slot }}
</span>
