@props(['options' => [], 'name' => null, 'value' => null, 'size' => 'sm'])

@php
    $wireModel = $attributes->wire('model');
    $name ??= $wireModel->value() ?: 'nav-switch-' . \Illuminate\Support\Str::random(8);
    $inputId = fn ($key) => \Illuminate\Support\Str::slug($name) . '-' . \Illuminate\Support\Str::slug((string) $key);
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:model')->class(['btn-group', 'btn-group-' . $size => $size]) }} role="group">
    @foreach ($options as $key => $option)
        @php
            $option = is_array($option) ? $option : ['label' => $option];
            $active = (string) $key === (string) $value;
        @endphp
        @isset($option['href'])
            <a href="{{ $option['href'] }}" @class(['btn', 'btn-outline-secondary', 'active' => $active]) @if ($active) aria-current="page" @endif>
                @isset($option['icon'])<i class="{{ $option['icon'] }}"></i>@endisset
                {{ $option['label'] ?? '' }}
            </a>
        @else
            <input
                type="radio"
                class="btn-check"
                name="{{ $name }}"
                id="{{ $inputId($key) }}"
                value="{{ $key }}"
                autocomplete="off"
                @if ($wireModel->value()) {{ $wireModel }} @endif
                @checked($active)
            >
            <label class="btn btn-outline-secondary" for="{{ $inputId($key) }}">
                @isset($option['icon'])<i class="{{ $option['icon'] }}"></i>@endisset
                {{ $option['label'] ?? '' }}
            </label>
        @endisset
    @endforeach
</div>
