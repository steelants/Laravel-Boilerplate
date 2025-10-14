<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('Backup') }}</h1>
            <div>
                <a class="btn btn-primary" href="{{ route('system.backup.run') }}"><i class="fas fa-robot me-2"></i><span>{{ __('Start Backup') }}</span></a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">{{ __('Name') }}</th>
                    <th scope="col">{{ __('Size') }}</th>
                    <th scope="col" class="text-end">{{ __('Actions') }}</th>
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
                            <td class="text-end">
                            @php($backups_slug = explode('_', $backup['fileName'][0])[0])
                                <a class="btn btn-sm btn-danger" title="{{ __('Remove') }}" href="{{ route('system.backup.delete', ['backup_date' => $backups_slug]) }}" onclick="return confirm('{{ __('Are you sure?') }}')">
                                    <div class="d-none d-md-none">
                                        {{ __('Remove') }}
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
</x-dynamic-component>
