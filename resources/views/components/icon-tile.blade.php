@use('SteelAnts\LaravelBoilerplate\Support\BootstrapTheme')
@props(['icon' => null, 'color' => 'primary', 'size' => 'md'])

@php
    $sizeClasses = match ($size) {
        'sm' => 'w-8 h-8 fs-md rounded-8',
        'lg' => 'w-14 h-14 fs-2xl rounded-8',
        default => 'w-10 h-10 fs-lg rounded-8',
    };
@endphp

<span {{ $attributes->class([
    'd-inline-flex align-items-center justify-content-center flex-shrink-0 fw-semibold lh-1',
    $sizeClasses,
    'bg-subtle-' . BootstrapTheme::theme($color),
    'fg-emphasis-' . BootstrapTheme::theme($color),
]) }} aria-hidden="true">
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @else
        {{ $slot }}
    @endif
</span>
