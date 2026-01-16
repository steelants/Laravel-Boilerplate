<?php

namespace App\Livewire\User;

use App\Models\User;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use SteelAnts\DataTable\Traits\UseDatabase;
use SteelAnts\LaravelBoilerplate\Traits\HasUsersPerPage;

class DataTable extends DataTableComponent
{
    use HasUsersPerPage;
    use UseDatabase;

    public $listeners = ['userAdded' => '$refresh'];

    private array $badgeCache = [];
    private array $priorityCache = [];
    private array $avatarCache = [];

    public function query(): Builder
    {
        return User::query();
    }

    public function headers(): array
    {
        return [
            'id'    => __('ID'),
            'name'  => __('Name'),
            'email' => __('E-mail'),
			'totp_force' => __('Enforce MFA')
        ];
    }

    public function actions($item)
    {
        if ($item['id'] == auth()->user()->id) {
            return [];
        }

        return [
            [
                'type'        => "livewire",
                'action'      => "edit",
                'parameters'  => $item['id'],
                'text'        => __("Edit"),
                'actionClass' => '',
                'iconClass'   => 'fas fa-pen',
            ],
            [
                'type'        => "livewire",
                'action'      => "remove",
                'parameters'  => $item['id'],
                'text'        => __("Remove"),
                'actionClass' => 'text-danger',
                'iconClass'   => 'fas fa-trash',
                'confirm' => __('Are you sure?'),
            ],
        ];
    }

    public function remove($user_id)
    {
        User::find($user_id)->delete();
    }

    public function edit($id)
    {
        $this->dispatch('openModal', 'user.form', __('Edit user'), ['user_id' => $id]);
    }

	   public function renderColumnTotpForce($value, $row)
    {
        $button = '';
        if (! empty($value)) {
            $button = '<i class="fa fa-check-circle text-success"></i>';
        } else {
            $button = '<i class="fa fa-times-circle text-danger"></i>';
        }

        return '<button class="btn btn-link" wire:click="change('.$row['id'].',\'totp_force\')">'.$button.'</button>';
    }

    public function change($id, $name)
    {
        $user = User::where('id', $id)->first();
        $data = [
            $name => ! $user->{$name},
        ];
        $user->update($data);
        $this->dispatch('closeModal');
    }
}
