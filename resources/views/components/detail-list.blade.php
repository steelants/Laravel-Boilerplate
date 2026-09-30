@props(['items' => []])

<dl {{ $attributes->class(['detail-list']) }}>
    @foreach ($items as $label => $value)
        <x-boilerplate::detail-list.item :label="$label">{{ $value }}</x-boilerplate::detail-list.item>
    @endforeach
    {{ $slot }}
</dl>
