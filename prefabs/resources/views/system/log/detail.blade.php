<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('File')}} - {{ $filename }}</h1>
            <div class="d-flex align-items-center gap-2">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="autoload-toggle" role="switch" checked>
                    <label class="form-check-label" for="autoload-toggle">{{ __('Auto-load') }}</label>
                </div>
                <a href='{{ route('system.log.download', ['file' => $filename]) }}' class="btn btn-primary">
                    <i class="fa fa-download"></i>
                </a>
                <a href='{{ route('system.log.delete', ['file' => $filename]) }}' onclick="return confirm('{{ __('Are you sure?') }}')" class="btn btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>

        <pre id="log-output" class="p-2 border" style="white-space: pre-wrap; word-break: break-all;">{{ implode("\n", $lines) }}</pre>
        <div id="end"></div>
    </div>

    @php
        $logTailConfig = [
            'url'    => route('system.log.tail', ['file' => $filename]),
            'offset' => $lineCount,
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
