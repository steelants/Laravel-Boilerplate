<x-dynamic-component :component="$layout">
    <div class="container-xl">
        <div class="page-header">
            <h1 class="hide-mobile">{{ __('Backup') }}</h1>
            <div>
                <a class="btn btn-primary" href="{{ route('system.backup.run') }}">
					<i class="fas fa-robot me-2"></i>
					<span>{{ __('Start Backup') }}</span>
				</a>
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
                            @php($hasDbZip = in_array($backups_slug . '_database.zip', $backup['fileName']))
                            @php($hasFsZip = in_array($backups_slug . '_storage.zip', $backup['fileName']))
                            @php($envBackedUp = config('boilerplate.backup.enviroment'))
                            @php($modalId = 'restoreModal-' . $backups_slug)

                            @if($hasDbZip || $hasFsZip)
                                <button type="button" class="btn btn-sm btn-warning" title="{{ __('Restore') }}"
                                        data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                    <div class="d-none d-md-none">
                                        {{ __('Restore') }}
                                    </div>
                                    <div class="d-inline d-md-inline">
                                        <i class="fa fa-rotate-left"></i>
                                    </div>
                                </button>

                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('system.backup.restore', ['backup_date' => $backups_slug]) }}">
                                            @csrf
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Restore Backup') }} &mdash; {{ $backups_slug }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="text-muted">{{ __('Select components to restore. This operation will overwrite current data.') }}</p>
                                                    @if($hasDbZip)
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="restore_database" value="1" id="db-{{ $backups_slug }}" checked>
                                                            <label class="form-check-label" for="db-{{ $backups_slug }}">
                                                                {{ __('Restore database') }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                    @if($hasFsZip)
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="restore_storage" value="1" id="st-{{ $backups_slug }}" checked>
                                                            <label class="form-check-label" for="st-{{ $backups_slug }}">
                                                                {{ __('Restore storage files') }}
                                                            </label>
                                                        </div>
                                                        @if($envBackedUp)
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input" name="restore_env" value="1" id="env-{{ $backups_slug }}">
                                                                <label class="form-check-label" for="env-{{ $backups_slug }}">
                                                                    {{ __('Restore .env file') }}
                                                                    <small class="text-warning d-block">{{ __('Warning: may change APP_KEY and invalidate sessions/encrypted data') }}</small>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fa fa-rotate-left me-1"></i>{{ __('Restore') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif

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
