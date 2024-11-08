<?php

namespace App\Livewire\Audit;

use App\Models\Activity;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use SteelAnts\DataTable\Traits\UseDatabase;

class DataTable extends DataTableComponent
{
    use UseDatabase;
    
    public bool $paginated = true;
    public int $itemsPerPage = 100;

    public function query(): Builder
    {
        return Activity::with(["affected", "actor"])->orderByDesc("created_at");
    }

    public function row($row): array
    {
        $affectedJson = json_encode([
            'id'=> $row->affected->id ?? '',
            'name'=> ($row->affected->title ??($row->affected->name ?? ($row->affected->description ?? ''))),
        ], JSON_UNESCAPED_UNICODE);

        return [
            'created_at' => $row->created_at,
            'ip_address' => $row->ip,
            'note' => $row->lang_text ,
            'actor_id' => ($row->actor->username ?? ($row->actor->name ?? __('System'))),
            'affected_id' => $affectedJson,
        ];
    }

    public function headers(): array
    {
        return [
            'created_at' => "Created",
            'ip_address' => "IP Address",
            'note' => "Note",
            'actor_id' => "Author",
            'affected_id' => "Model"
        ];
    }
}
