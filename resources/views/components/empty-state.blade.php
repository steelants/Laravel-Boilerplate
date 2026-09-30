@props(['icon' => 'fas fa-inbox', 'title' => null, 'description' => null, 'color' => 'secondary'])

<div {{ $attributes->class(['empty-state']) }}>
    @if ($icon)
        <x-boilerplate::icon-tile :icon="$icon" :color="$color" size="lg" class="mb-3" />
    @endif
    @if ($title)
        <div class="empty-state-title">{{ $title }}</div>
    @endif
    @if ($description)
        <div class="empty-state-description">{{ $description }}</div>
    @endif
    @if ($slot->isNotEmpty())
        <div class="empty-state-actions">{{ $slot }}</div>
    @endif
</div>
