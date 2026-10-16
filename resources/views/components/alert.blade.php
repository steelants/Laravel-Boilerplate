@props(['type' => 'info', 'title' => null, 'icon' => null, 'dismissible' => false])

@php
    $color = $type === 'error' ? 'danger' : $type;
    $icon ??= match ($color) {
        'success' => 'fas fa-check-circle',
        'danger' => 'fas fa-times-circle',
        'warning' => 'fas fa-exclamation-triangle',
        default => 'fas fa-info-circle',
    };
@endphp

<div {{ $attributes->class(['alert', 'alert-' . $color, 'd-flex', 'align-items-start', 'gap-3', 'fade show' => $dismissible]) }} role="alert">
    @if ($icon)
        <i class="{{ $icon }} fs-5 lh-base flex-shrink-0"></i>
    @endif

    <div class="flex-grow-1 min-w-0">
        @if ($title)
            <div class="fw-semibold">{{ $title }}</div>
        @endif
        @if ($slot->isNotEmpty())
            <div>{{ $slot }}</div>
        @endif
    </div>

    @isset($actions)
        <div {{ $actions->attributes->class(['d-flex', 'flex-wrap', 'gap-2', 'flex-shrink-0']) }}>{{ $actions }}</div>
    @endisset

    @if ($dismissible)
        <button type="button" class="btn-close flex-shrink-0 mt-1" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    @endif
</div>
