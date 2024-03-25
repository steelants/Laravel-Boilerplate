<?php

namespace App\Livewire\User;

use App\Models\User;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;

class DataTable extends DataTableComponent
{
    public $listeners = [
        'userAdded' => '$refresh'
    ];

    public function query(): Builder
    {
        return User::query();
    }

    public function row($row): array
    {
        return [
            'id' => $row->id,
            'name' => $row->name,
            'email' => $row->email,
        ];
    }

    public function headers(): array
    {
        return ["id", "name", "email"];
    }

    public function actions($item)
    {
        if ($item['id'] == auth()->user()->id){
            return [];
        }

        return [
            [
                'type' => "livewire",
                'action' => "remove",
                'name' => "remove",
                'text' => "remove",
                'parameters' => $item['id']
            ]
        ];
    }

    public function remove($user_id){
        User::find($user_id)->delete();
    }
}
