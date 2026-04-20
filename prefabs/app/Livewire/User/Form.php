<?php

namespace App\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use SteelAnts\LivewireForm\Livewire\FormComponent;
use SteelAnts\Modal\Livewire\Attributes\AllowInModal;

#[AllowInModal('is-system-admin')]
class Form extends FormComponent
{
    public $user_id;

    protected function rules()
    {
        return [
            'properties.name'     => 'required|max:255',
            'properties.email'    => 'required|string|email|max:255|unique:users,email' . (!empty($this->user_id) ? ',' . $this->user_id : ''),
            'properties.password' => (empty($this->user_id) ? 'required' : 'sometimes') . '|string|min:8|max:255|confirmed',
        ];
    }

    #[Computed()]
    public function fields()
    {
        return ['name', 'email', 'password', 'password_confirmation'];
    }

    public function properties()
    {
        if (!empty($this->user_id)) {
            $user = User::find($this->user_id);

            return [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'password'              => '',
                'password_confirmation' => '',
            ];
        }

        return [
            'name'                  => '',
            'email'                 => '',
            'password'              => '',
            'password_confirmation' => '',
        ];
    }

    #[Computed()]
    public function types()
    {
        return [
            'password'              => 'password',
            'password_confirmation' => 'password',
        ];
    }

    #[Computed()]
    public function labels()
    {
        return [
            'name'                  => __('Name'),
            'email'                 => __('Email'),
            'password'              => __('Password'),
            'password_confirmation' => __('Confirm Password'),
        ];
    }

    public function mount($user_id = null)
    {
        $this->user_id = $user_id;
        parent::mount();
    }

    public function submit(): bool
    {
        Gate::authorize('is-system-admin');

        $data = $this->properties;
        unset($data['password_confirmation']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (!empty($this->user_id)) {
            $user = User::find($this->user_id);
            $this->authorize('update', $user);
            $user->update($data);
            $this->dispatch('snackbar', ['message' => __('User updated'), 'type' => 'success', 'icon' => 'fas fa-check']);
        } else {
            $this->authorize('create', User::class);
            User::create($data);
            $this->dispatch('snackbar', ['message' => __('User created'), 'type' => 'success', 'icon' => 'fas fa-check']);
        }

        return true;
    }

    public function onSuccess()
    {
        $this->dispatch('close-modal');
        $this->dispatch('userAdded');
    }
}
