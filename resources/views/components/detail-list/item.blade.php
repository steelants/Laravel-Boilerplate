@props(['label'])

<div {{ $attributes->class(['list-group-item', 'px-0', 'd-flex', 'flex-column', 'sm:flex-row', 'sm:gap-3']) }}>
    <dt class="fw-normal fg-3 sm:col-4 lg:col-3">{{ $label }}</dt>
    <dd class="mb-0 min-w-0 text-break">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <span class="fg-4">&mdash;</span>
        @endif
    </dd>
</div>
