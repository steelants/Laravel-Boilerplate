<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
             <h1>{{ __('Cache') }} - {{ $cache_driver }}</h1>
             <button onclick="confirm('{{ __('Do you really want to clear all data in cache?') }}') ? window.location.href = '{{ route('system.cache.clear') }}' : false" class="btn btn-danger">{{ __('Clear cashes') }}</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Key') }}</th>
                        <th>{{ __('Expire at') }}</th>
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
