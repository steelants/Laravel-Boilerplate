<x-layout-app>
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

                    </tr>
                </thead>
                <tbody>
                    @foreach ($cache_items as $item)
                        <tr>
                            <th scope="row">
                                {{ var_dump($item) }}
                            </th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    </div>
</x-layout-app>
