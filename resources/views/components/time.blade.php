@use(\Illuminate\Support\Carbon)

@props(['datetime' => null, 'format' => 'LLL', 'relative' => true])

@php($date = filled($datetime) ? Carbon::parse($datetime) : null)

@if ($date)
    <time
        datetime="{{ $date->toIso8601String() }}"
        {{ $attributes->class(['text-nowrap']) }}
        @if ($relative) title="{{ $date->isoFormat($format) }}" x-data="bsTooltip()" @endif
    >{{ $relative ? $date->diffForHumans() : $date->isoFormat($format) }}</time>
@else
    <span {{ $attributes->class(['text-body-tertiary']) }}>{{ $slot->isNotEmpty() ? $slot : '—' }}</span>
@endif
