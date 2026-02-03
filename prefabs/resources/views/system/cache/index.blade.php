<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
             <h1 class="hide-mobile" >{{ __('Cache') }} - {{ $cache_driver }}</h1>
             <div class="d-flex align-items-center gap-2">
                 @if ($opcache['loaded'])
                     <div class="badge {{ $opcache['enabled'] ? 'bg-success' : 'bg-warning' }}">
                         OPcache: {{ $opcache['enabled'] ? __('Enabled') : __('Disabled') }}
                         @if ($opcache['enabled'])
                             ({{ $opcache['memory_used'] }}MB / {{ $opcache['memory_free'] }}MB {{ __('free') }})
                         @endif
                     </div>
                     @if ($opcache['enabled'])
                         <div class="badge {{ $opcache['validate_timestamps'] ? 'bg-info' : 'bg-danger' }}" title="opcache.validate_timestamps">
                             {{ __('Validate') }}: {{ $opcache['validate_timestamps'] ? __('ON') : __('OFF') }}
                             @if ($opcache['validate_timestamps'])
                                 ({{ $opcache['revalidate_freq'] }}s)
                             @endif
                         </div>
                     @endif
                 @else
                     <div class="badge bg-secondary">OPcache: {{ __('Not loaded') }}</div>
                 @endif
                 <button onclick="confirm('{{ __('Do you really want to clear all data in cache?') }}') ? window.location.href = '{{ route('system.cache.clear') }}' : false" class="btn btn-danger">{{ __('Clear cashes') }}</button>
             </div>
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
</x-dynamic-component>
