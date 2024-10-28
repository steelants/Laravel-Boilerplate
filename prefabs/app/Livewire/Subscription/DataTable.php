<?php

namespace App\Livewire\Subscription;

use App\Models\Subscription;
use App\Models\User;
use App\Types\SubscriptionTier;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;

class DataTable extends DataTableComponent
{
    public $listeners = [
        'subscriptionRefresh' => '$refresh'
    ];

    public function query(): Builder
    {
        return Subscription::query();
    }

    public function row($row): array
    {
        return [
            'id' => $row->id,
            'tier' => SubscriptionTier::getName($row->tier),
            'valid_to' => $row->valid_to->format('d. m. Y'),
        ];
    }

    public function headers(): array
    {
        return ["id", "tier", "valid_to"];
    }

    public function actions($item)
    {
        return [
            [
                'type' => "livewire",
                'action' => "edit",
                'text' => "edit",
                'parameters' => $item['id']
            ]
        ];
    }

    public function edit($id)
    {
        $this->dispatch('openModal', 'subscription.form', __('boilerplate::subscriptions.edit'), $id);
    }
}
