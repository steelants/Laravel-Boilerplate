<?php

namespace App\Livewire\Session;

use Illuminate\Database\Eloquent\Builder;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use SteelAnts\DataTable\Traits\UseDatabase;
use SteelAnts\LaravelBoilerplate\RenderCasts\FormatDateTime;

class DataTable extends DataTableComponent
{
    use UseDatabase;

    public bool $paginated = false;

    public bool $sortable = false;

    public function query(): Builder
    {
        return request()->user()->sessions()->orderByDesc('last_activity')->getQuery();
    }

    public function headers(): array
    {
        return [
            'ip_address'       => __('IP Address'),
            'browser_os_name' => __('OS Name'),
            'browser_name'     => __('Browser'),
            'last_activity'    => __('Last Activity'),
        ];
    }

    public function renderCasts(): array
    {
        return [
            'last_activity' => FormatDateTime::class,
        ];
    }

    public function actions($item): array
    {
        return [
            [
                'type'        => 'livewire',
                'action'      => 'logout',
                'parameters'  => $item['id'],
                'text'        => __('Logout'),
                'actionClass' => 'text-danger',
                'iconClass'   => 'fas fa-trash text-danger',
                'confirm'     => __('Are you sure?'),
            ],
        ];
    }

    public function logout($session_id)
    {
        request()->user()->sessions()->find($session_id)->delete();
        alert()->success(__('Session logged out'))->reload();

        return redirect(request()->header('Referer'));
    }
}
