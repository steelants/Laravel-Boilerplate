@props(['value'])

<span {{ $attributes->class(['copy']) }} x-data="copyToClipboard(@js((string) $value))">
    @if ($slot->isNotEmpty())
        <span class="copy-text">{{ $slot }}</span>
    @endif
    <button
        type="button"
        class="btn btn-sm copy-btn"
        x-on:click="copy()"
        :title="copied ? @js(__('Copied')) : @js(__('Copy'))"
        aria-label="{{ __('Copy') }}"
    >
        <i class="fa-fw" :class="copied ? 'fas fa-check text-success' : 'far fa-copy'"></i>
    </button>
</span>
