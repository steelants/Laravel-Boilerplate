<?php

namespace App\Livewire\User;

use App\Models\User;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use SteelAnts\DataTable\Traits\UseDatabase;

class DataTable extends DataTableComponent
{
    use UseDatabase;

    public $listeners = ['userAdded' => '$refresh'];

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
                'action'      => "remove",
                'parameters'  => $item['id'],
                'text'        => "Remove",
                'actionClass' => 'text-danger',
                'iconClass'   => 'fas fa-trash',
            ],
        ];
    }

    public function remove($user_id)
    {
        User::find($user_id)->delete();
    }
}
