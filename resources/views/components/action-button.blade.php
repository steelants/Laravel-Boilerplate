@props([
    'action' => null,
    'target' => null,
    'confirm' => null,
    'icon' => null,
    'variant' => 'primary',
    'size' => null,
    'type' => 'button',
    'disabled' => false,
    'disabledReason' => null,
    'tooltip' => null,
])

@php
    $action ??= $attributes->wire('click')->value() ?: null;
    $target ??= $action;
    $tooltipText = $disabled ? ($disabledReason ?? $tooltip) : $tooltip;
@endphp

@if ($disabled && $tooltipText)
    <span class="d-inline-block" tabindex="0" title="{{ $tooltipText }}" x-data="bsTooltip()">
@endif
<button
    type="{{ $type }}"
    {{ $attributes->class(['btn', 'd-inline-flex', 'align-items-center', 'justify-content-center', 'gap-2', 'btn-' . $variant, 'btn-' . $size => $size]) }}
    @if ($action && !$attributes->wire('click')->value()) wire:click="{{ $action }}" @endif
    @if ($confirm) wire:confirm="{{ $confirm }}" @endif
    @if ($target) wire:target="{{ $target }}" @endif
    @if ($disabled) disabled aria-disabled="true" @else wire:loading.attr="disabled" @endif
    @if (!$disabled && $tooltipText) title="{{ $tooltipText }}" x-data="bsTooltip()" @endif
>
    @if ($icon)
        <i class="{{ $icon }}" wire:loading.remove @if ($target) wire:target="{{ $target }}" @endif></i>
    @endif
    <span class="spinner-border spinner-border-sm" wire:loading @if ($target) wire:target="{{ $target }}" @endif role="status" aria-hidden="true"></span>
    {{ $slot }}
</button>
@if ($disabled && $tooltipText)
    </span>
@endif
