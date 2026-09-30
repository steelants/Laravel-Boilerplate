@props(['href' => null, 'icon' => null, 'danger' => false, 'active' => false, 'disabled' => false])

<li>
    <{{ $href ? 'a' : 'button' }}
        @if ($href) href="{{ $href }}" @else type="button" @endif
        {{ $attributes->class(['dropdown-item', 'text-danger' => $danger, 'active' => $active, 'disabled' => $disabled]) }}
        @if ($disabled) aria-disabled="true" @if (!$href) disabled @endif @endif
    >
        @if ($icon)
            <i class="dropdown-item-icon fa-fw {{ $icon }} {{ $danger ? 'text-danger' : '' }}"></i>
        @endif
        <span>{{ $slot }}</span>
    </{{ $href ? 'a' : 'button' }}>
</li>
