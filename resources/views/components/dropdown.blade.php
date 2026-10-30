@use('SteelAnts\LaravelBoilerplate\Support\BootstrapTheme')
@props([
    'icon' => 'fa-solid fa-ellipsis-v',
    'label' => null,
    'variant' => 'light',
    'size' => 'sm',
    'align' => 'end',
    'caret' => false,
])

<div {{ $attributes->class(['d-inline-block']) }}>
    @isset($trigger)
        <div data-bs-toggle="menu" data-bs-placement="bottom-{{ $align }}" aria-expanded="false" {{ $trigger->attributes }}>{{ $trigger }}</div>
    @else
        <button
            type="button"
            @class(['btn', BootstrapTheme::button($variant), 'btn-' . $size => $size])
            data-bs-toggle="menu"
            data-bs-placement="bottom-{{ $align }}"
            aria-expanded="false"
            @unless ($label) aria-label="{{ __('Actions') }}" @endunless
        >
            @if ($icon)
                <i class="{{ $icon }}"></i>
            @endif
            {{ $label }}
        </button>
    @endisset

    <ul class="menu">
        {{ $slot }}
    </ul>
</div>
