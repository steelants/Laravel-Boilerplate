<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Types\SettingDataType;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Limitation extends Component
{
    public $user;
    public $limitation = 10;

    protected function rules()
    {
        return ['limitation' => 'nullable|numeric|min:10|max:1000'];
    }

    public function mount(User $user)
    {
        $this->user = $user;
        if (!empty($user->limitationSetting)) {
            $this->limitation = $user->limitationSetting;
        }
    }

    #[Computed]
    public function limits()
    {
        return [
            10   => 10,
            20   => 20,
            50   => 50,
            100  => 100,
            1000 => 1000,
        ];
    }

    public function store()
    {
        $validatedData = $this->validate();
        $this->user->settings()->updateOrCreate(
            ['index' => "limitation.items_per_page"],
            [
                'value' => $validatedData["limitation"],
                'type'  => SettingDataType::INT,
            ],
        );

        $this->dispatch('close-modal');
        $this->dispatch('snackbar', ['message' => __('Saved'), 'type' => 'success', 'icon' => 'fas fa-check']);

        $this->dispatch('limitationSaved');
    }

    public function render()
    {
        return view('livewire.user.limitation');
    }
}
