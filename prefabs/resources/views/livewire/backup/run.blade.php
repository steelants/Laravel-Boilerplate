@use(\SteelAnts\LaravelBoilerplate\Helpers\SizeHelper)
@use(\SteelAnts\LaravelBoilerplate\Types\BackupStatusType)

<div class="text-center py-3" @if ($inProgress) wire:poll.2s="refreshStatus" @endif>
    @if ($inProgress)
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
        <p class="mb-0">
            {{ BackupStatusType::getName($state) }}
        </p>
        @if ($waitingTooLong)
            <small class="text-warning d-block mt-2">{{ __('The backup has not started yet. Make sure a queue worker is running.') }}</small>
        @endif
    @elseif ($state === BackupStatusType::FINISHED)
        {{-- The page lists the backups, reload it once the modal is closed to show the new one. --}}
        <div x-init="$el.closest('.modal')?.addEventListener('hidden.bs.modal', () => window.location.reload(), {once: true})">
            <i class="fa-solid fa-check-circle fa-3x text-success mb-3"></i>
            <p class="mb-1">{{ $status['message'] ?? BackupStatusType::getName($state) }}</p>
            @if ($duration)
                <small class="text-muted d-block">{{ __('Took :duration', ['duration' => $duration]) }}</small>
            @endif
            @if (!empty($status['files']))
                <ul class="list-group text-start mt-3">
                    @foreach ($status['files'] as $file)
                        <li class="list-group-item d-flex align-items-center gap-2">
                            <span class="flex-grow-1">{{ $file['name'] }}</span>
                            <span class="text-muted">{{ SizeHelper::getHumanReadableSize($file['size']) }}</span>
                            <a href="{{ route('system.backup.download', ['file_name' => $file['name']]) }}" class="btn btn-sm btn-primary" title="{{ __('Download') }}">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @elseif ($state === null)
        <i class="fa-solid fa-question-circle fa-3x text-warning mb-3"></i>
        <p class="mb-1">{{ __('Backup status is not available') }}</p>
        <small class="text-muted d-block mb-3">{{ __('The web server and the queue worker have to share the same cache store.') }}</small>
        <x-form::button class="btn-secondary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @else
        <i class="fa-solid fa-times-circle fa-3x text-danger mb-3"></i>
        <p class="mb-1">{{ BackupStatusType::getName(BackupStatusType::FAILED) }}</p>
        @if (!empty($status['message']))
            <small class="text-muted d-block mb-3">{{ $status['message'] }}</small>
        @endif
        <x-form::button class="btn-secondary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @endif
</div>
