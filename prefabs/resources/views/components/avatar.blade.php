<div {{ $attributes->class(['avatar', 'avatar-'.$size, 'random-bg-'.$color]) }}" title="{{ $name }}">
    @if (!empty($image))
        <img src="{{ $image }}" alt="{{ $short }}">
    @else
        {{ $short }}
    @endif
</div>
