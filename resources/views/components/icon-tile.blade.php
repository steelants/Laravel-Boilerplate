@props(['icon' => null, 'color' => 'primary', 'size' => 'md'])

@php
    $sizeClasses = match ($size) {
        'sm' => 'w-8 h-8 fs-6 rounded-2',
        'lg' => 'w-14 h-14 fs-3 rounded-3',
        default => 'w-10 h-10 fs-5 rounded-3',
    };
@endphp

<span {{ $attributes->class([
    'd-inline-flex align-items-center justify-content-center flex-shrink-0 fw-semibold lh-1',
    $sizeClasses,
    'bg-' . $color . '-subtle',
    'text-' . $color . '-emphasis',
]) }} aria-hidden="true">
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @else
        {{ $slot }}
    @endif
</span>
