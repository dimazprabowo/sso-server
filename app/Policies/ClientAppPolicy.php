<?php

namespace App\Policies;

use App\Models\ClientApp;
use App\Models\User;

class ClientAppPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('client_apps_view');
    }

    public function create(User $user): bool
    {
        return $user->can('client_apps_create');
    }

    public function update(User $user, ClientApp $clientApp): bool
    {
        return $user->can('client_apps_update');
    }

    public function delete(User $user, ClientApp $clientApp): bool
    {
        return $user->can('client_apps_delete');
    }

    public function toggleActive(User $user, ClientApp $clientApp): bool
    {
        return $user->can('client_apps_update');
    }

    public function regenerateSecret(User $user, ClientApp $clientApp): bool
    {
        return $user->can('client_apps_manage');
    }

    public function testConnection(User $user): bool
    {
        return $user->can('client_apps_update');
    }

    public function manage(User $user, ClientApp $clientApp): bool
    {
        return $user->can('client_apps_manage');
    }
}
