<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Form extends Component
{
    public $user_id;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules()
    {
        return [
            'name'     => 'required|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email' . (!empty($this->user_id) ? "," . $this->user_id : ""),
            'password' => (empty($this->user_id) ? 'required' : 'sometimes').'|string|min:8|max:255|confirmed',
        ];
    }

    public function mount($user_id = null)
    {
        $this->user_id = $user_id;

        if (!empty($this->user_id)) {
            $user = User::find($this->user_id);
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function render()
    {
        return view('livewire.user.form');
    }

    public function store()
    {
        $this->authorize('create', User::class);

        $validatedData = $this->validate();

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        if (!empty($this->user_id)) {
            $user = User::find($this->user_id);
            $this->authorize('update', $user);
            $user->update($validatedData);

            $this->dispatch('snackbar', ['message' => __('Uživatel upraven'), 'type' => 'success', 'icon' => 'fas fa-check']);
        } else {
            $this->authorize('create', User::class);
            User::create($validatedData);
            $this->dispatch('snackbar', ['message' => __('boilerplate::ui.create'), 'type' => 'success', 'icon' => 'fas fa-check']);
        }

        $this->dispatch('close-modal');
        $this->dispatch('userAdded');

        $this->reset('user_id');
        $this->reset('name');
        $this->reset('email');
        $this->reset('password');
        $this->reset('password_confirmation');
    }
}
