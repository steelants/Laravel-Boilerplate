@props(['value' => null, 'max' => 100, 'label' => null, 'color' => 'primary', 'percent' => true, 'height' => null])

@php
    $indeterminate = $value === null;
    $percentage = $indeterminate || !$max ? null : max(0, min(100, round($value / $max * 100)));
@endphp

<div {{ $attributes }}>
    @if ($label || ($percent && !$indeterminate))
        <div class="d-flex justify-content-between gap-3 small mb-1">
            <span>{{ $label }}</span>
            @if ($percent && !$indeterminate)
                <span class="text-body-secondary">{{ $percentage }}&nbsp;%</span>
            @endif
        </div>
    @endif
    <div
        class="progress"
        role="progressbar"
        @if ($label) aria-label="{{ $label }}" @endif
        @unless ($indeterminate) aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" @endunless
        @if ($height) style="height: {{ $height }}" @endif
    >
        @if ($indeterminate)
            <div class="progress-bar progress-bar-striped progress-bar-animated w-100p bg-{{ $color }}"></div>
        @else
            <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%"></div>
        @endif
    </div>
</div>
