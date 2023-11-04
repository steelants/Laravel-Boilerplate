<?php

namespace App\Http\Livewire\Subscription;

use App\Models\Subscription;
use Livewire\Component;
use App\Types\SubscriptionTier;

class Form extends Component
{
    public $model;
    public $tier;
    public $valid_to;

    public $tiers;
    public $action = 'store';

    protected function rules()
    {
        return [
            'tier' => 'required|integer|min:1',
            'valid_to' => 'required|date',
        ];
    }

    public function mount($model = null)
    {
        $this->tiers = SubscriptionTier::getNames();

        if (!empty($model)) {
            $sub = Subscription::find($model);
            if (empty($sub)) {
                return;
            }

            $this->model = $model;
            $this->tier = $sub->tier;
            $this->valid_to = $sub->valid_to->format('Y-m-d');
            $this->action = 'update';
        }
    }

    public function render()
    {
        return view('livewire.subscription.form');
    }

    public function store()
    {
        $validatedData = $this->validate();

        Subscription::create($validatedData);

        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('snackbar', ['message' => __('boilerplate::ui.item-created'), 'type' => 'success', 'icon' => 'fas fa-check']);

        $this->emit('subscriptionRefresh');

        $this->reset('tier');
        $this->reset('valid_to');
    }

    public function update()
    {
        $validatedData = $this->validate();

        $sub = Subscription::find($this->model);
        if (!empty($sub)) {
            $sub->update($validatedData);
        }

        $this->dispatchBrowserEvent('close-modal');
        $this->dispatchBrowserEvent('snackbar', ['message' => __('boilerplate::ui.item-updated'), 'type' => 'success', 'icon' => 'fas fa-check']);

        $this->emit('subscriptionRefresh');

        $this->reset('tier');
        $this->reset('valid_to');
    }
}
