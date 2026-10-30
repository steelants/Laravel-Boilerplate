<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('File')}} - {{ $filename }}</h1>
            <div class="d-flex align-items-center gap-2">
                <div class="form-field mb-0">
                    <div class="switch">
                        <input type="checkbox" id="autoload-toggle" role="switch" checked>
                    </div>
                    <label for="autoload-toggle">{{ __('Auto-load') }}</label>
                </div>
                <a href='{{ route('system.log.download', ['file' => $filename]) }}' class="btn btn-solid theme-primary">
                    <i class="fa-solid fa-download"></i>
                </a>
                <x-boilerplate::form-button :action="route('system.log.delete', ['file' => $filename])" :confirm="__('Are you sure?')" :title="__('Remove')" />
            </div>
        </div>

        <pre id="log-output" class="p-2 border" style="white-space: pre-wrap; word-break: break-all;">{{ implode("\n", $lines) }}</pre>
        <div id="end"></div>
    </div>

    @php
        $logTailConfig = [
            'url'    => route('system.log.tail', ['file' => $filename]),
            'offset' => $offset,
        ];
    @endphp
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initLogTail(@json($logTailConfig));
        });
    </script>
    @endpush
</x-dynamic-component>
