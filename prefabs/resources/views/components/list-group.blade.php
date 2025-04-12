<ul {{ $attributes->class('list-group') }}>
    @foreach ($items as $item)
        <li class="list-group-item">{{ $item }}</li>
    @endforeach
</ul>
