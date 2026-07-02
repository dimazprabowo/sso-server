<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles_view');
    }

    public function create(User $user): bool
    {
        return $user->can('roles_create');
    }

    public function update(User $user, Role $role): bool
    {
        if ($role->name === 'super-admin') {
            return false;
        }

        return $user->can('roles_update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($role->name === 'super-admin') {
            return false;
        }

        if ($role->users()->count() > 0) {
            return false;
        }

        return $user->can('roles_delete');
    }
}
