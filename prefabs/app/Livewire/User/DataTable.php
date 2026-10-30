<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use SteelAnts\DataTable\Traits\UseDatabaseEloquent;
use SteelAnts\LaravelBoilerplate\Traits\HasUsersPerPage;

class DataTable extends DataTableComponent
{
    use HasUsersPerPage;
    use UseDatabaseEloquent;

    public $listeners = ['userAdded' => '$refresh'];

    private array $badgeCache = [];

    private array $priorityCache = [];

    private array $avatarCache = [];

    public function query(): Builder
    {
        return User::query();
    }

    public function row($row): array
    {
        return [
            'id'         => $row->id,
            'name'       => $row->name,
            'email'      => $row->email,
            'totp_force' => $row->totp_force,
        ];
    }

    public function headers(): array
    {
        return [
            'id'         => __('ID'),
            'name'       => __('Name'),
            'email'      => __('E-mail'),
            'totp_force' => __('Enforce MFA'),
        ];
    }

    public function actions($item): array
    {
        if ($item['id'] == auth()->user()->id && !Gate::allows('is-system-admin')) {
            return [];
        }

        return [
            [
                'type'        => 'livewire',
                'action'      => 'edit',
                'parameters'  => $item['id'],
                'text'        => __('Edit'),
                'actionClass' => '',
                'iconClass'   => 'fa-solid fa-pen',
            ],
            [
                'type'        => 'livewire',
                'action'      => 'remove',
                'parameters'  => $item['id'],
                'text'        => __('Remove'),
                'actionClass' => 'fg-danger',
                'iconClass'   => 'fa-solid fa-trash',
                'confirm'     => __('Are you sure?'),
            ],
        ];
    }

    public function renderColumnName(mixed $value, $row): string
    {
        if (!$row->isSystemAdmin) {
            return e($value);
        }

        return e($value) . ' <span class="badge bg-danger fg-contrast-danger ms-1">' . __('System Admin') . '</span>';
    }

    public function renderColumnTotpForce($value, $row): string
    {
        $button = !empty($value)
            ? '<i class="fa-solid fa-check-circle fg-success"></i>'
            : '<i class="fa-solid fa-times-circle fg-danger"></i>';

        return '<button class="btn btn-link text-decoration-none" wire:click="change(' . $row['id'] . ',\'totp_force\')">' . $button . '</button>';
    }

    public function edit($id)
    {
        $this->dispatch('openModal', 'user.form', __('Edit user'), ['user_id' => $id]);
    }

    public function remove($user_id)
    {
        Gate::authorize('is-system-admin');
        User::find($user_id)->delete();
        alert()->success(__('User removed'))->now();
    }

    public function change($id, $name)
    {
        Gate::authorize('is-system-admin');
        $user = User::where('id', $id)->first();
        $user->update([$name => !$user->{$name}]);
        alert()->success(__('Updated'))->now();
        $this->dispatch('closeModal');
    }
}
