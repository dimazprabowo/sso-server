<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RolePermissionService
{
    public function getAllRolesWithPermissions(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function getRolePermissions(int $roleId): array
    {
        $role = Role::with('permissions')->find($roleId);

        return $role ? $role->permissions->pluck('name')->toArray() : [];
    }

    public function createRole(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name, 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function updateRole(Role $role, string $name, array $permissions = []): Role
    {
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }

    public function roleHasUsers(Role $role): bool
    {
        return $role->users()->count() > 0;
    }

    /**
     * Build permission groups with human-readable labels.
     *
     * Returns: ['Group Name' => [['name' => 'permission_key', 'label' => 'Human Label'], ...]]
     */
    public function buildPermissionGroups(): array
    {
        $groupMapping = [
            'Dashboard' => [
                ['name' => 'dashboard_view', 'label' => 'Lihat Dashboard'],
            ],
            'Kelola User' => [
                ['name' => 'users_view',        'label' => 'Lihat User'],
                ['name' => 'users_create',      'label' => 'Tambah User'],
                ['name' => 'users_update',      'label' => 'Edit User'],
                ['name' => 'users_delete',      'label' => 'Hapus User'],
                ['name' => 'users_impersonate', 'label' => 'Impersonate User'],
            ],
            'Role & Permission' => [
                ['name' => 'roles_view',   'label' => 'Lihat Roles'],
                ['name' => 'roles_create', 'label' => 'Tambah Role'],
                ['name' => 'roles_update', 'label' => 'Edit Role'],
                ['name' => 'roles_delete', 'label' => 'Hapus Role'],
            ],
            'Client Apps' => [
                ['name' => 'client_apps_view',   'label' => 'Lihat Client Apps'],
                ['name' => 'client_apps_create', 'label' => 'Tambah Client App'],
                ['name' => 'client_apps_update', 'label' => 'Edit Client App'],
                ['name' => 'client_apps_delete', 'label' => 'Hapus Client App'],
                ['name' => 'client_apps_manage', 'label' => 'Kelola Client Apps'],
            ],
            'Akses Aplikasi' => [
                ['name' => 'user_access_view',   'label' => 'Lihat Akses Aplikasi'],
                ['name' => 'user_access_update', 'label' => 'Kelola Akses Aplikasi'],
            ],
        ];

        $allPermissions = Permission::pluck('name')->toArray();
        $mapped = collect($groupMapping)->flatten(1)->pluck('name')->toArray();
        $unmapped = array_diff($allPermissions, $mapped);

        $groups = $groupMapping;

        if (!empty($unmapped)) {
            $groups['Lainnya'] = array_values(array_map(
                fn($p) => ['name' => $p, 'label' => ucwords(str_replace('_', ' ', $p))],
                $unmapped
            ));
        }

        return $groups;
    }

    /**
     * Get a flat map of permission name => label for display purposes.
     */
    public function getPermissionLabels(): array
    {
        $labels = [];
        foreach ($this->buildPermissionGroups() as $groupPerms) {
            foreach ($groupPerms as $perm) {
                $labels[$perm['name']] = $perm['label'];
            }
        }

        return $labels;
    }
}
