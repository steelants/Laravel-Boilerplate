<?php

namespace App\Livewire\Backup;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use SteelAnts\LaravelBoilerplate\Attributes\AllowInModal;
use SteelAnts\LaravelBoilerplate\Services\BackupService;
use SteelAnts\LivewireForm\Livewire\FormComponent;

#[AllowInModal(ability: 'is-system-admin')]
class Restore extends FormComponent
{
    #[Locked]
    public string $backup_date;

    public string $viewName = 'livewire.backup.restore';

    protected function rules()
    {
        return [
            'properties.restore_database' => 'boolean',
            'properties.restore_storage'  => 'boolean',
            'properties.restore_env'      => 'boolean',
        ];
    }

    #[Computed()]
    public function fields()
    {
        $fields = [];

        if ($this->hasArchive('database')) {
            $fields[] = 'restore_database';
        }

        if ($this->hasArchive('storage')) {
            $fields[] = 'restore_storage';
            if (config('boilerplate.backup.enviroment')) {
                $fields[] = 'restore_env';
            }
        }

        return $fields;
    }

    public function properties()
    {
        return [
            'restore_database' => $this->hasArchive('database'),
            'restore_storage'  => $this->hasArchive('storage'),
            'restore_env'      => false,
        ];
    }

    #[Computed()]
    public function types()
    {
        return [
            'restore_database' => 'checkbox',
            'restore_storage'  => 'checkbox',
            'restore_env'      => 'checkbox',
        ];
    }

    #[Computed()]
    public function labels()
    {
        return [
            'restore_database' => __('Restore database'),
            'restore_storage'  => __('Restore storage files'),
            'restore_env'      => __('Restore .env file'),
        ];
    }

    #[Computed()]
    public function helps()
    {
        return [
            'restore_env' => __('Warning: may change APP_KEY and invalidate sessions/encrypted data'),
        ];
    }

    public function mount($backup_date = null)
    {
        abort_unless(config('boilerplate.backup.restore', true) && BackupService::parseDate((string) $backup_date), 404);
        $this->backup_date = $backup_date;

        parent::mount();
    }

    public function submit(): bool
    {
        Gate::authorize('is-system-admin');

        try {
            $restored = BackupService::restore(
                $this->backup_date,
                (bool) $this->properties['restore_database'],
                (bool) $this->properties['restore_storage'],
                (bool) $this->properties['restore_env'],
            );
        } catch (\Throwable $e) {
            alert()->error(__('Restore failed') . ': ' . $e->getMessage())->now();

            return false;
        }

        if (!$restored) {
            alert()->error(__('Select at least one component to restore'))->now();
        }

        return $restored;
    }

    public function onSuccess()
    {
        // The restore may replace the sessions table, so reload the whole page instead of just closing the modal.
        alert()->success(__('Restore completed'));
        $this->redirectRoute('system.backup.index');
    }

    public function onError()
    {
        // DO SOMETHING ON ERROR;
    }

    #[Computed()]
    public function archives(): array
    {
        return BackupService::list()[$this->backup_date]['fileName'] ?? [];
    }

    private function hasArchive(string $type): bool
    {
        return in_array($this->backup_date . '_' . $type . '.zip', $this->archives);
    }
}
