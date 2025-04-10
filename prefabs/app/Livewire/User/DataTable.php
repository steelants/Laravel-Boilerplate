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
            'id'    => 'ID',
            'name'  => 'Name',
            'email' => 'E-mail',
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
                'text'        => __("Upravit"),
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
            ],
        ];
    }

    public function remove($user_id)
    {
        User::find($user_id)->delete();
    }

    public function edit($id)
    {
        $this->dispatch('openModal', 'user.form', __('Editovat uživatele'), ['user_id' => $id]);
    }
}
