<div {{ $attributes->class('calendar calendar-'.$type) }}>
    <div class="calendar-area">
        <div class="calendar-times">
            @foreach ($times as $time)
                <div class="calendar-time @if ($time == $selectedTime) calendar-time-active @endif">
                    {{ $time }}</div>
            @endforeach
        </div>
        <div class="calendar-days">
            <div class="calendar-gridlines">
                @foreach ($times as $time)
                    <div class="calendar-gridline"></div>
                @endforeach
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
