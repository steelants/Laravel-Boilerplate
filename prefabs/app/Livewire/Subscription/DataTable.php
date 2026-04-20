<?php

namespace App\Livewire\Subscription;

use Illuminate\Database\Eloquent\Builder;
use SteelAnts\DataTable\Livewire\DataTableComponent;
use SteelAnts\DataTable\Traits\UseDatabase;
use SteelAnts\LaravelBoilerplate\Models\Subscription;
use SteelAnts\LaravelBoilerplate\Types\SubscriptionTier;

class DataTable extends DataTableComponent
{
    use UseDatabase;

    public $listeners = ['subscriptionRefresh' => '$refresh'];

    public function query(): Builder
    {
        return Subscription::query();
    }

    public function row($row): array
    {
        return [
            'id'       => $row->id,
            'tier'     => SubscriptionTier::getName($row->tier),
            'valid_to' => $row->valid_to->format('d. m. Y'),
        ];
    }

    public function headers(): array
    {
        return [
            'id'       => __('ID'),
            'tier'     => __('Tier'),
            'valid_to' => __('Valid to'),
        ];
    }

    public function actions($item): array
    {
        return [
            [
                'type'       => 'livewire',
                'action'     => 'edit',
                'parameters' => $item['id'],
                'text'       => __('Edit'),
            ],
        ];
    }

    public function edit($id)
    {
        $this->dispatch('openModal', 'subscription.form', __('Edit subscription'), [$id]);
    }
}
