@props(['value' => null, 'max' => 100, 'label' => null, 'color' => 'primary', 'percent' => true, 'height' => null])

@php
    $indeterminate = $value === null;
    $percentage = $indeterminate || !$max ? null : max(0, min(100, round($value / $max * 100)));
@endphp

<div {{ $attributes->class(['progress-box']) }}>
    @if ($label || ($percent && !$indeterminate))
        <div class="progress-box-header">
            <span class="progress-box-label">{{ $label }}</span>
            @if ($percent && !$indeterminate)
                <span class="progress-box-value">{{ $percentage }}&nbsp;%</span>
            @endif
        </div>
    @endif
    <div
        @class(['progress', 'progress-indeterminate' => $indeterminate])
        role="progressbar"
        @if ($label) aria-label="{{ $label }}" @endif
        @unless ($indeterminate) aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" @endunless
        @if ($height) style="height: {{ $height }}" @endif
    >
        <div class="progress-bar bg-{{ $color }}" @unless ($indeterminate) style="width: {{ $percentage }}%" @endunless></div>
    </div>
</div>
