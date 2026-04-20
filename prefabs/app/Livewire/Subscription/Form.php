<?php

namespace App\Livewire\Subscription;

use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use SteelAnts\LaravelBoilerplate\Models\Subscription;
use SteelAnts\LaravelBoilerplate\Types\SubscriptionTier;
use SteelAnts\LivewireForm\Livewire\FormComponent;
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

#[AllowInModal('is-system-admin')]
class Form extends FormComponent
{
    public $model;

    protected function rules()
    {
        return [
            'properties.tier'     => 'required|integer|min:1',
            'properties.valid_to' => 'required|date',
        ];
    }

    #[Computed()]
    public function fields()
    {
        return ['tier', 'valid_to'];
    }

    public function properties()
    {
        if (!empty($this->model)) {
            $sub = Subscription::find($this->model);

            return [
                'tier'     => $sub->tier,
                'valid_to' => $sub->valid_to->format('Y-m-d'),
            ];
        }

        return [
            'tier'     => '',
            'valid_to' => '',
        ];
    }

    #[Computed()]
    public function types()
    {
        return [
            'tier'     => 'select',
            'valid_to' => 'date',
        ];
    }

    #[Computed()]
    public function labels()
    {
        return [
            'tier'     => __('Tier'),
            'valid_to' => __('Valid To'),
        ];
    }

    public function tier_options(): array
    {
        return SubscriptionTier::getNames();
    }

    public function mount(?int $model = null)
    {
        if (!empty($model)) {
            $this->model = $model;
        }

        parent::mount();
    }

    public function submit(): bool
    {
        Gate::authorize('is-system-admin');

        if (!empty($this->model)) {
            $sub = Subscription::find($this->model);
            if (!empty($sub)) {
                $sub->update($this->properties);
            }
            alert()->success(__('Item successfully updated'))->now();
        } else {
            Subscription::create($this->properties);
            alert()->success(__('Item successfully created'))->now();
        }

        return true;
    }

    public function onSuccess()
    {
        $this->dispatch('close-modal');
        $this->dispatch('subscriptionRefresh');
    }
}
