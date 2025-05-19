<div class="gantt-frame border"
    data-scale="{{ $scale }}"
    style="
        --gantt-col-with: {{ $stepWidth }}px;
    "
>
<div class="gantt-frame-data">
    <div class="gantt-frame-data-header"></div>
    @foreach ($rows as $row)
    <div class="gantt-data-item">
        {{ $row['title'] }}
    </div>
    @endforeach
</div>
<div class="gantt-timeline-frame">
    <div class="gantt-timeline-header">
        @foreach($steps as $group => $gitems)
            <div class="gantt-timeline-header-group">
                <div class="gantt-timeline-header-group-name" title="{{$group}}">{{$group}}</div>
                <div class="gantt-timeline-header-items">
                    @foreach ($gitems as $item)
                        <div class="gantt-timeline-header-item @if($item['isActive']) gantt-timeline-header-item-active @endif">
                            <span>{{ $item['title'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>


    <div class="gantt-timeline">
        <div class="gantt-timeline-grid">
            @foreach($steps as $group => $gitems)
                <div class="gantt-timeline-grid-group">
                    @foreach ($gitems as $item)
                        <div class="
                            gantt-timeline-grid-line
                            @if($item['isInactive']) gantt-timeline-grid-line-inactive @endif
                            @if($item['isActive']) gantt-timeline-grid-line-active @endif
                        "></div>
                    @endforeach
                </div>
            @endforeach
        </div>

        @foreach ($rows as $row)
            <div class="gantt-timeline-item">
                @foreach ($row['items'] as $item)
                    @if(isset($item['url'])) 
                        <a target="_blank" href="{{$item['url']}}" 
                    @else 
                        <div
                    @endif      
                        class="gantt-timeline-item-indicator"
                        title="{{$item['title']}}"
                        style="background-color: {{$item['color']}};width: {{$item['width']}}px; left: {{$item['left']}}px;"
                    >
                        @if(isset($item['progress']))
                            <div class="gantt-timeline-item-progress" style="background-color: var(--bs-{{$item['color']}});width: {{$item['progressWidth']}}px;">
                            </div>
                        @endif
                    @if(isset($item['url'])) 
                        </a>
                    @else 
                        </div>
                    @endif 
                @endforeach
            </div>
        @endforeach

    </div>
</div>
</div>
