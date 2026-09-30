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

<div {{ $attributes->class(['alert', 'alert-' . $color, 'alert-box', 'fade show' => $dismissible]) }} role="alert">
    @if ($icon)
        <i class="alert-box-ico {{ $icon }}"></i>
    @endif

    <div class="alert-box-body">
        @if ($title)
            <div class="alert-box-title">{{ $title }}</div>
        @endif
        @if ($slot->isNotEmpty())
            <div class="alert-box-text">{{ $slot }}</div>
        @endif
    </div>

    @isset($actions)
        <div {{ $actions->attributes->class(['alert-box-actions']) }}>{{ $actions }}</div>
    @endisset

    @if ($dismissible)
        <button type="button" class="btn-close alert-box-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
    @endif
</div>
