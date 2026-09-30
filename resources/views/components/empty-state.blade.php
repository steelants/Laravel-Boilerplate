@props(['icon' => 'fas fa-inbox', 'title' => null, 'description' => null, 'color' => 'secondary'])

<div {{ $attributes->class(['d-flex', 'flex-column', 'align-items-center', 'text-center', 'py-5', 'px-3']) }}>
    @if ($icon)
        <x-boilerplate::icon-tile :icon="$icon" :color="$color" size="lg" class="mb-3" />
    @endif
    @if ($title)
        <div class="fw-semibold">{{ $title }}</div>
    @endif
    @if ($description)
        <div class="text-body-secondary mt-1">{{ $description }}</div>
    @endif
    @if ($slot->isNotEmpty())
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">{{ $slot }}</div>
    @endif
</div>
