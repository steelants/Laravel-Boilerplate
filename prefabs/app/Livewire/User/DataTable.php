<?php

namespace App\Livewire\User;

use App\Models\User;
use SteelAnts\DataTable\Http\Livewire\DataTableV2;
use Illuminate\Database\Eloquent\Builder;

class DataTable extends DataTableV2
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
                'parameters' => $item['id']
            ]
        ];
    }

    public function remove($user_id){
        User::find($user_id)->delete();
    }
}
