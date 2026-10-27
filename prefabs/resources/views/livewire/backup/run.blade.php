<div class="text-center py-3" @if ($inProgress) wire:poll.2s="refreshStatus" @endif>
    @if ($inProgress)
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
        <p class="mb-0">
            {{ \SteelAnts\LaravelBoilerplate\Types\BackupStatusType::getName($state) }}
        </p>
        @if ($waitingTooLong)
            <small class="text-warning d-block mt-2">{{ __('The backup has not started yet. Make sure a queue worker is running.') }}</small>
        @endif
    @elseif ($state === \SteelAnts\LaravelBoilerplate\Types\BackupStatusType::FINISHED)
        <i class="fas fa-circle-check fa-3x text-success mb-3"></i>
        <p>{{ $status['message'] ?? \SteelAnts\LaravelBoilerplate\Types\BackupStatusType::getName($state) }}</p>
        <x-form::button class="btn-primary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @else
        <i class="fas fa-circle-xmark fa-3x text-danger mb-3"></i>
        <p class="mb-1">{{ \SteelAnts\LaravelBoilerplate\Types\BackupStatusType::getName(\SteelAnts\LaravelBoilerplate\Types\BackupStatusType::FAILED) }}</p>
        @if (!empty($status['message']))
            <small class="text-muted d-block mb-3">{{ $status['message'] }}</small>
        @endif
        <x-form::button class="btn-secondary" onclick="window.location.reload()">{{ __('Close') }}</x-form::button>
    @endif
</div>
