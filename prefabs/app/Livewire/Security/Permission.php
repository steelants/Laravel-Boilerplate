<?php

namespace App\Livewire\Security;

use App\Models\User;
use Livewire\Component;
use SteelAnts\LaravelPermission\Models\Role;
use SteelAnts\LaravelPermission\Traits\HasRoles;

class Permission extends Component
{
    public User $user;

    /** @var array<int> */
    public array $assignedRoleIds = [];

    public string $newScope = '';

    public bool $usesRoles = false;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->usesRoles = in_array(HasRoles::class, class_uses_recursive($user));

        if ($this->usesRoles) {
            $this->assignedRoleIds = $user->roles()->pluck('role_definitions.id')->toArray();
        }
    }

    public function toggleRole(int $roleId): void
    {
        $role = Role::findOrFail($roleId);

        if (in_array($roleId, $this->assignedRoleIds)) {
            $this->user->removeRole($role);
            $this->assignedRoleIds = array_values(array_diff($this->assignedRoleIds, [$roleId]));
        } else {
            $this->user->assignRole($role);
            $this->assignedRoleIds[] = $roleId;
        }
    }

    public function addScope(): void
    {
        $ability = trim($this->newScope);

        if ($ability === '') {
            return;
        }

        $this->user->addScope($ability);
        $this->user->refresh();
        $this->newScope = '';
    }

    public function removeScope(string $ability): void
    {
        $this->user->removeScope($ability);
        $this->user->refresh();
    }

    public function render()
    {
        return view('livewire.security.permission', [
            'allRoles' => $this->usesRoles ? Role::orderBy('name')->get() : collect(),
        ]);
    }
}
