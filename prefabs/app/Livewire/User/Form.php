<?php

namespace App\Http\User;

use App\Http\Requests\System\CreateUserRequest;
use Livewire\Component;
use App\Models\User;

class Form extends Component
{
    public string $name ='';
    public string $email ='';
    public string $password ='';
    public string $password_confirmation ='';

    protected function rules()
    {
        return [
            'name' => 'required|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
        ];
    }

    public function render()
    {
        return view('livewire.user.form');
    }

    public function store()
    {
        $validatedData = $this->validate();
        User::create($validatedData);
        
        $this->dispatch('close-modal');
        $this->dispatch('snackbar', ['message' => __('boilerplate::ui.create'), 'type' => 'success', 'icon' => 'fas fa-check']);

        $this->dispatch('userAdded');
        
        $this->reset('name');
        $this->reset('email');
        $this->reset('password');
        $this->reset('password_confirmation');
    }
}


