<?php

namespace App\Livewire\Session;

use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use SteelAnts\DataTable\Traits\UseDatabase;

class DataTable extends DataTableComponent
{
    use UseDatabase;

    public bool $paginated = false;
    public bool $sortable = false;

    public function query(): Builder
    {
        return request()->user()->sessions()->orderByDesc("last_activity")->getQuery();
    }

    public function row($row): array
    {
        return [
            'id'              => $row->id,
            'ip_address'      => $row->ip_address,
            'last_activity'   => $row->last_activity->format('d. m. Y H:m'),
            'browser_os_name' => $row->browser_o_s_name,
            'browser_name'    => $row->browser_name,
            'last_activity'   => $row->last_activity->format('d. m. Y H:m'),

        ];
    }

    public function headers(): array
    {
        return [
            'ip_address'      => __("IP Address"),
            'browser_os_name' => __("OS Name"),
            'browser_name'    => __("Browser"),
            'last_activity'   => __("Last Activity"),
        ];
    }

    public function actions($item)
    {
        return [
            [
                'type'        => "livewire",
                'action'      => "logout",
                'parameters'  => $item['id'],
                'text'        => __("Logout"),
                'actionClass' => 'text-danger',
				'iconClass'   => 'fas fa-trash text-danger',
                'confirm' => __('Are you sure?'),
            ],
        ];
    }

    public function logout($session_id)
    {
        request()->user()->sessions()->find($session_id)->delete();
        return redirect(request()->header('Referer'));
    }
}
