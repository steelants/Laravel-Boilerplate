<div class="dropdown ms-auto">
    @if (count($items))
        <button class="btn btn-sq" data-bs-toggle="dropdown" type="button">
            <i class="bi bi-three-dots"></i>
        </button>
        <div class="dropdown-menu">
            @foreach ($items as $item)
                @if ($item->isLink())
                    <a @class(['dropdown-item', $item->actionClass()]) href="{{ $item->url }}" wire:key="action-{{ $item->name }}">
                        <i class="dropdown-ico {{ $item->iconClass() }}"></i>{{ $item->text }}
                    </a>
                @else
                    <button @class(['dropdown-item', $item->actionClass()]) type="button" wire:key="action-{{ $item->name }}"
                        wire:click="run(@js($item->name))"
                        @if ($item->confirm) wire:confirm="{{ $item->confirm }}" @endif>
                        <i class="dropdown-ico {{ $item->iconClass() }}"></i>{{ $item->text }}
                    </button>
                @endif
            @endforeach
        </div>
    @endif
</div>
