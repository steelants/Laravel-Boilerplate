<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('boilerplate::ui.backup') }}</h1>
            <div>
                <a class="btn btn-primary" href="{{ route('system.backup.run') }}"><i class="fas fa-robot me-2"></i><span>{{ __('boilerplate::ui.start_backup') }}</span></a>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">{{ __('boilerplate::ui.name') }}</th>
                    <th scope="col">{{ __('boilerplate::ui.size') }}</th>
                    <th scope="col" class="text-end">{{ __('boilerplate::ui.actions') }}</th>
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
                                <a class="btn btn-sm btn-danger" title="{{ __('web.delete') }}" href="{{ route('system.backup.delete', ['backup_date' => $backups_slug]) }}" onclick="return confirm('{{ __('backup.delete_confirmation') }}')">
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
</x-dynamic-component>
