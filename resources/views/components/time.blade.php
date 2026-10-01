@props(['datetime' => null, 'format' => 'LLL', 'relative' => true])

@php
    $date = match (true) {
        $datetime instanceof \DateTimeInterface => \Illuminate\Support\Carbon::instance($datetime),
        filled($datetime) => \Illuminate\Support\Carbon::parse($datetime),
        default => null,
    };
@endphp

@if ($date)
    <time
        datetime="{{ $date->toIso8601String() }}"
        {{ $attributes->class(['text-nowrap']) }}
        @if ($relative) title="{{ $date->isoFormat($format) }}" x-data="bsTooltip()" @endif
    >{{ $relative ? $date->diffForHumans() : $date->isoFormat($format) }}</time>
@else
    <span {{ $attributes->class(['text-body-tertiary']) }}>{{ $slot->isNotEmpty() ? $slot : '—' }}</span>
@endif
