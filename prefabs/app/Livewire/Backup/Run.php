<?php

namespace App\Livewire\Backup;

use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;
use SteelAnts\LaravelBoilerplate\Services\BackupService;
use SteelAnts\LaravelBoilerplate\Types\BackupStatusType;

#[AllowInModal(ability: 'is-system-admin')]
class Run extends Component
{
    public ?array $status = null;

    public function mount()
    {
        Gate::authorize('is-system-admin');

        // Opening the modal while a backup is already running only shows its progress.
        BackupService::dispatch();
        $this->refreshStatus();
    }

    public function refreshStatus()
    {
        $this->status = BackupService::status();
    }

    public function render()
    {
        return view('livewire.backup.run', [
            'state'      => $this->status['state'] ?? null,
            'inProgress' => BackupStatusType::isInProgress($this->status['state'] ?? null),
            // A queued backup that never starts usually means no queue worker is running.
            'waitingTooLong' => ($this->status['state'] ?? null) === BackupStatusType::QUEUED
                && now()->timestamp - ($this->status['updated_at'] ?? 0) > 30,
        ]);
    }
}
