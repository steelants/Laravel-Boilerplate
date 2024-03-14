<x-layout-app>
    <div class="container">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.backup') }}</h1>
            <div>
                <a class="btn btn-primary" href="{{ route('system.backup.run') }}"><i class="fas fa-robot me-2"></i><span>{{ __('boilerplate::backup.start_export') }}</span></a>
                <a class="btn btn-outline-primary" href="{{ route('system.backup.download.latest') }}" target="_blank"><i class="fas fa-download me-2"></i><span>{{ __('backup.download_last_backup') }}</span></a>
            </div>
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
                                @foreach ($backup['fileName'] as $key => $fileName)
                                    {{ ($key != 0 ? ", " : "") }}<a href="{{ route('system.backup.download', ['file_name' => $fileName]) }}">{{ $fileName }}</a>
                                @endforeach
                            </th>
                            <td>
                                {{ $backup['fileSize'] }}
                            </td>
                            <td>
                                <a class="btn btn-sm btn-danger" title="{{ __('web.delete') }}" href="{{ route('system.backup.delete', ['file_name' => $backup['fileNameTotal']]) }}" onclick="return confirm('{{ __('backup.delete_confirmation') }}')">
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
