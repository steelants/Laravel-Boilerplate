<?php

namespace App\Http\Livewire\Audit;

use App\Models\Activity;
use SteelAnts\DataTable\Http\Livewire\DataTableV2;
use Illuminate\Database\Eloquent\Builder;

class DataTable extends DataTableV2
{
    public bool $paginated = true;
    public int $itemsPerPage = 100;

    public function query(): Builder
    {
        return Activity::with(["affected", "user"])->orderByDesc("created_at");
    }

    public function row($row): array
    {
        $affectedJson = json_encode([
            'id'=>$row->affected->id ?? '',
            'name'=>$row->affected->name ?? '',
        ], JSON_UNESCAPED_UNICODE);

        return [
            'created_at' => $row->created_at,
            'ip_address' => $row->ip,
            'note' => $row->lang_text ,
            'user_id' => ($row->user->name ?? 'Unknown'),
            'affected_id' => $affectedJson,
        ];
    }

    public function headers(): array
    {
        return ["Created", "IP Address", "Note", "Author", "Model"];
    }
}