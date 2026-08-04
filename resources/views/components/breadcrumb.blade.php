@if (count($items))
    <nav {{ $attributes }} aria-label="{{ __('Breadcrumb') }}">
        <ol class="breadcrumb">
            @foreach ($items as $path => $label)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ url($path) }}">{{ $label }}</a></li>
                @endif
            @endforeach
        </ol>
    </nav>
@endif
