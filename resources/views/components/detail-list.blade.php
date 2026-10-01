@props(['items' => []])

<dl {{ $attributes->class(['list-group', 'list-group-flush', 'mb-0']) }}>
    @foreach ($items as $label => $value)
        <x-boilerplate::detail-list.item :label="$label">{{ $value }}</x-boilerplate::detail-list.item>
    @endforeach
    {{ $slot }}
</dl>
