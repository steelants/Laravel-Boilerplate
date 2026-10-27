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
        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
        <p>{{ $status['message'] ?? BackupStatusType::getName($state) }}</p>
        <x-form::button class="btn-primary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @elseif ($state === null)
        <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
        <p class="mb-1">{{ __('Backup status is not available') }}</p>
        <small class="text-muted d-block mb-3">{{ __('The web server and the queue worker have to share the same cache store.') }}</small>
        <x-form::button class="btn-secondary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @else
        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
        <p class="mb-1">{{ BackupStatusType::getName(BackupStatusType::FAILED) }}</p>
        @if (!empty($status['message']))
            <small class="text-muted d-block mb-3">{{ $status['message'] }}</small>
        @endif
        <x-form::button class="btn-secondary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @endif
</div>
