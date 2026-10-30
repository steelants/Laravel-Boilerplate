@props(['options' => [], 'name' => null, 'value' => null, 'size' => 'sm'])

@php
    $wireModel = $attributes->wire('model');
    $name ??= $wireModel->value() ?: 'nav-switch-' . \Illuminate\Support\Str::random(8);
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:model')->class(['btn-group', 'btn-group-' . $size => $size]) }} role="group">
    @foreach ($options as $key => $option)
        @php
            $option = is_array($option) ? $option : ['label' => $option];
            $active = (string) $key === (string) $value;
        @endphp
        @isset($option['href'])
            <a href="{{ $option['href'] }}" @class(['btn', 'btn-outline theme-secondary', 'active' => $active]) @if ($active) aria-current="page" @endif>
                @isset($option['icon'])<i class="{{ $option['icon'] }}"></i>@endisset
                {{ $option['label'] ?? '' }}
            </a>
        @else
            <label class="btn btn-check btn-outline theme-secondary">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $key }}"
                    autocomplete="off"
                    @if ($wireModel->value()) {{ $wireModel }} @endif
                    @checked($active)
                >
                @isset($option['icon'])<i class="{{ $option['icon'] }}"></i>@endisset
                {{ $option['label'] ?? '' }}
            </label>
        @endisset
    @endforeach
</div>
