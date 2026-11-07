<div @class(['d-inline-block' => $asButton, 'dropdown ms-auto' => !$asButton])>
    @if (count($items))
        @unless ($asButton)
            <button class="btn btn-sq" data-bs-toggle="dropdown" type="button">
                <i class="bi bi-three-dots"></i>
            </button>
        @endunless
        <div @class(['dropdown-menu' => !$asButton])>
            @foreach ($items as $item)
                @php
                    $classes = $asButton
                        ? ['btn', 'btn-danger' => $item->destructive, 'btn-secondary' => !$item->destructive]
                        : ['dropdown-item', $item->actionClass()];
                    $icon = $asButton ? $item->icon . ' me-2' : 'dropdown-ico ' . $item->iconClass();
                @endphp
                @if ($item->isLink())
                    <a @class($classes) href="{{ $item->url }}" wire:key="action-{{ $item->name }}">
                        <i class="{{ $icon }}"></i>{{ $item->text }}
                    </a>
                @else
                    <button @class($classes) type="button" wire:key="action-{{ $item->name }}"
                        wire:click="run(@js($item->name))"
                        @if ($item->confirm) wire:confirm="{{ $item->confirm }}" @endif>
                        <i class="{{ $icon }}"></i>{{ $item->text }}
                    </button>
                @endif
            @endforeach
        </div>
    @endif
</div>
