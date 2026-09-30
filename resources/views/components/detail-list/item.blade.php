@props(['label'])

<div {{ $attributes->class(['detail-list-item']) }}>
    <dt class="detail-list-label">{{ $label }}</dt>
    <dd class="detail-list-value">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <span class="text-body-tertiary">&mdash;</span>
        @endif
    </dd>
</div>
