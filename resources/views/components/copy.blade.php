@props(['value'])

<span {{ $attributes->class(['d-inline-flex', 'align-items-center', 'gap-1', 'mw-100']) }} x-data="copyToClipboard(@js((string) $value))">
    @if ($slot->isNotEmpty())
        <span class="text-truncate">{{ $slot }}</span>
    @endif
    <button
        type="button"
        class="btn btn-sm btn-link text-body-secondary py-0 px-1"
        x-on:click="copy()"
        :title="copied ? @js(__('Copied')) : @js(__('Copy'))"
        aria-label="{{ __('Copy') }}"
    >
        <i class="fa-fw" :class="copied ? 'fas fa-check text-success' : 'far fa-copy'"></i>
    </button>
</span>
