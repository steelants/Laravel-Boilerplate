<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.file')}} - {{ $filename }}</h1>
            <a href='{{ route('system.log.download', ['file' => $filename]) }}' class="btn btn-secondary">{{ __('boilerplate::ui.logs-download') }}</a>
        </div>

        <pre class="p-2 border">{!! $content !!}</pre>

        <div id="end"></div>
    </div>
</x-layout-app>
