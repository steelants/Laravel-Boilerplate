<div class="calendar-day @if ($active) calendar-day-active @endif" wire:key="day_{{ $day }}">
    <div class="calendar-day-name">{{ Illuminate\Support\Carbon::parse($day)->format('D d. m. Y') }}</div>
    <div class="calendar-items">
        {{ $slot }}
    </div>
</div>
