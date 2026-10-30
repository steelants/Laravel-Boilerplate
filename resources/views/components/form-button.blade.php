@use('SteelAnts\LaravelBoilerplate\Support\BootstrapTheme')
@props([
    'action',
    'confirm' => null,
    'label'   => null,
    'title'   => null,
    'icon'    => 'fa-solid fa-trash',
    'variant' => 'danger',
])
<form method="POST" action="{{ $action }}" class="{{ $variant ? 'd-inline' : 'd-block' }}"
    @if ($confirm) onsubmit="return confirm(@js($confirm))" @endif>
    @csrf
    <button type="submit" title="{{ $title ?? $label }}" {{ $attributes->class(array_filter(['btn', $variant ? BootstrapTheme::button($variant) : null])) }}>
        <i @class([$icon, 'me-2' => $label && $variant])></i>@if ($label)<span>{{ $label }}</span>@endif
    </button>
</form>
