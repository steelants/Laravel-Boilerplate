@props([
    'icon' => 'fas fa-ellipsis-v',
    'label' => null,
    'variant' => 'light',
    'size' => 'sm',
    'align' => 'end',
    'caret' => false,
])

<div {{ $attributes->class(['dropdown', 'd-inline-block']) }}>
    @isset($trigger)
        <div data-bs-toggle="dropdown" aria-expanded="false" {{ $trigger->attributes }}>{{ $trigger }}</div>
    @else
        <button
            type="button"
            @class(['btn', 'btn-' . $variant, 'btn-' . $size => $size, 'dropdown-toggle' => $caret])
            data-bs-toggle="dropdown"
            aria-expanded="false"
            @unless ($label) aria-label="{{ __('Actions') }}" @endunless
        >
            @if ($icon)
                <i class="{{ $icon }}"></i>
            @endif
            {{ $label }}
        </button>
    @endisset

    <ul @class(['dropdown-menu', 'dropdown-menu-' . $align => $align])>
        {{ $slot }}
    </ul>
</div>
