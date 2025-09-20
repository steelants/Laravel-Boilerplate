<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.cache') }}</h1>
             <button onclick="confirm('{{ __('boilerplate::ui.cache-clear-confirm') }}') ? window.location.href = '{{ route('system.cache.clear') }}' : false" class="btn btn-danger">{{ __('boilerplate::ui.cache-clear') }}</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">key</th>
                        <th>expire_at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cache_items as $item)
                        <tr>
                            <td scope="row">
                                {{ $item['key'] }}
                            </td>
                            <td>
                                {{ $item['expire_at'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div>
</x-dynamic-component>
