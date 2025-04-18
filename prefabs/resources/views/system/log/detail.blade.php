<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.file')}} - {{ $filename }}</h1>
            <div>
                <a href='{{ route('system.log.download', ['file' => $filename]) }}' class="btn btn-secondary">
                    <i class="fa fa-download"></i>
                </a>
                <a href='{{ route('system.log.delete', ['file' => $filename]) }}' onclick="return confirm('{{ __('log.delete_confirmation') }}')" class="btn btn-danger">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>

        <pre class="p-2 border">{!! $content !!}</pre>

        <div id="end"></div>
    </div>
</x-layout-app>
