<x-layout-app>
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.backup') }}</h1>
        </div>
        {{-- @livewire('backup.data-table', [], key('data-table')) --}}
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">{{ __('boilerplate::ui.name') }}</th>
                    <th scope="col">{{ __('boilerplate::ui.size') }}</th>
                    <th scope="col">{{ __('boilerplate::ui.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($backups))
                    @foreach ($backups as $backup)
                        <tr>
                            <th scope="row">
                                <a href="{{ route('system.backups.download', ['file_name' => $backup['fileName']]) }}">{{ $backup['fileName'] }}</a>
                            </th>
                            <td>
                                {{ $backup['humanReadableSize'] }}
                            </td>
                            <td>
                                <a class="btn btn-sm btn-danger" title="{{ __('web.delete') }}" href="{{ route('system.backups.delete', ['file_name' => $backup['fileName']]) }}" onclick="return confirm('{{ __('backup.delete_confirmation', ['backupFileName' => $backup['fileName']]) }}')">
                                    <div class="d-none d-md-none">
                                        {{ __('web.delete') }}
                                    </div>
                                    <div class="d-inline d-md-inline">
                                        <i class="fa fa-trash"></i>
                                    </div>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</x-layout-app>
