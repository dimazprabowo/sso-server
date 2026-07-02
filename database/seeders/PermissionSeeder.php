<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Single source of truth untuk semua permissions SSO Server.
     *
     * Idempotent — aman dijalankan berulang kali:
     *   php artisan db:seed --class=PermissionSeeder
     *
     * Konvensi penamaan:
     *   {entity}_{action}
     *   entity : dashboard, users, roles, client_apps, user_access
     *   action : view, create, update, delete, manage
     *
     * Format ini memudahkan grouping otomatis di UI berdasarkan entity prefix.
     *
     * Permission khusus:
     *   - client_apps_manage: Akses ke halaman kelola client app (icon gear)
     *                         Mencakup kelola user & role di remote app via DB/API sync
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard_view',

            // Kelola User
            'users_view',
            'users_create',
            'users_update',
            'users_delete',
            'users_impersonate',

            // Role & Permission
            'roles_view',
            'roles_create',
            'roles_update',
            'roles_delete',

            // Client Apps
            'client_apps_view',
            'client_apps_create',
            'client_apps_update',
            'client_apps_delete',
            'client_apps_manage',

            // Akses Aplikasi (User ↔ App)
            'user_access_view',
            'user_access_update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
