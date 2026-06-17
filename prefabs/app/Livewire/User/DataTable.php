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
                'iconClass'   => 'fas fa-pen',
            ],
            [
                'type'        => 'livewire',
                'action'      => 'remove',
                'parameters'  => $item['id'],
                'text'        => __('Remove'),
                'actionClass' => 'text-danger',
                'iconClass'   => 'fas fa-trash',
                'confirm'     => __('Are you sure?'),
            ],
        ];
    }

    public function renderColumnName(mixed $value, $row): string
    {
        if (!$row->isSystemAdmin) {
            return e($value);
        }

        return e($value) . ' <span class="badge text-bg-danger ms-1">' . __('System Admin') . '</span>';
    }

    public function renderColumnTotpForce($value, $row): string
    {
        $button = !empty($value)
            ? '<i class="fa fa-check-circle text-success"></i>'
            : '<i class="fa fa-times-circle text-danger"></i>';

        return '<button class="btn btn-link" wire:click="change(' . $row['id'] . ',\'totp_force\')">' . $button . '</button>';
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
