<?php

namespace App\Livewire\Session;

use App\Models\Session;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DataTable extends DataTableComponent
{
    public bool $paginated = false;
    public bool $sortable = false;

    public function query(): Builder
    {
        return request()->user()->sessions()->orderByDesc("created_at")->getQuery();
    }

    public function row($row): array
    {
        return [
            'id' => $row->id,
            'ip_address' => $row->ip_address,
            'last_activity' => $row->last_activity->format('d. m. Y H:m'),
            'browser_os_name' => $row->browser_os_name,
            'browser_name' => $row->browser_name,
            'last_activity' => $row->last_activity->format('d. m. Y H:m'),

        ];
    }

    public function headers(): array
    {
        return [
            'ip_address' => "IP Address",
            'browser_os_name' => "OS Name",
            'browser_name' => "Browser",
            'last_activity' => "Last Activity"
        ];
    }

    public function actions($item)
    {
        return [
            [
                'type' => "livewire",
                'action' => "logout",
                'parameters' => $item['id'],
                'text' => "Logout",
                'actionClass' => 'text-danger',
                'iconClass' => 'fas fa-trash',
            ]
        ];
    }

    public function logout($session_id)
    {
        request()->user()->sessions()->find($session_id)->delete();
        return redirect(request()->header('Referer'));
    }
}
