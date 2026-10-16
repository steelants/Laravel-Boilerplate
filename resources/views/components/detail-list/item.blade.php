@props(['label'])

<div {{ $attributes->class(['list-group-item', 'px-0', 'd-flex', 'flex-column', 'flex-sm-row', 'gap-sm-3']) }}>
    <dt class="fw-normal text-body-secondary col-sm-4 col-lg-3">{{ $label }}</dt>
    <dd class="mb-0 min-w-0 text-break">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <span class="text-body-tertiary">&mdash;</span>
        @endif
    </dd>
</div>
