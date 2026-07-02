<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users_view');
    }

    public function create(User $user): bool
    {
        return $user->can('users_create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users_update');
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        return $user->can('users_delete');
    }

    public function toggleActive(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        return $user->can('users_update');
    }

    public function impersonate(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if (session()->has('impersonate_original_id')) {
            return false;
        }

        return $user->can('users_impersonate');
    }
}
