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
                'confirm' => __('Are you shure?'),
            ],
        ];
    }

    public function remove($user_id)
    {
        User::find($user_id)->delete();
    }

    public function edit($id)
    {
        $this->dispatch('openModal', 'user.form', __('Edit :model', ['model' => __('user')]), ['user_id' => $id]);
    }
}
