<?php

namespace App\Services;

use App\Models\ClientApp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RemoteAppService
{
    private const CONNECTION_NAME = '_remote_app';

    public function testDatabaseConnection(ClientApp $app): array
    {
        if (! $app->hasDatabaseConfig()) {
            return ['success' => false, 'message' => 'Konfigurasi database tidak lengkap.'];
        }

        try {
            $this->connectDatabase($app);
            DB::connection(self::CONNECTION_NAME)->getPdo();
            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => 'Koneksi database berhasil.'];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function testApiConnection(ClientApp $app): array
    {
        if (! $app->hasApiConfig()) {
            return ['success' => false, 'message' => 'Konfigurasi API tidak lengkap.'];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-SSO-Secret' => $app->api_secret_key,
                    'Accept' => 'application/json',
                ])
                ->get(rtrim($app->api_base_url, '/') . '/sso/ping');

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Koneksi API berhasil.'];
            }

            return ['success' => false, 'message' => 'Gagal: HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function syncUserToApp(ClientApp $app, array $userData): array
    {
        if ($app->hasDatabaseConfig()) {
            return $this->syncUserViaDatabase($app, $userData);
        }

        if ($app->hasApiConfig()) {
            return $this->syncUserViaApi($app, $userData);
        }

        return ['success' => false, 'message' => 'Tidak ada metode sinkronisasi yang dikonfigurasi.'];
    }

    public function removeUserFromApp(ClientApp $app, int $userId): array
    {
        if ($app->hasApiConfig()) {
            return $this->removeUserViaApi($app, $userId);
        }

        if ($app->hasDatabaseConfig()) {
            return $this->removeUserViaDatabase($app, $userId);
        }

        return ['success' => false, 'message' => 'Tidak ada metode sinkronisasi yang dikonfigurasi.'];
    }

    public function getRemoteUsers(ClientApp $app): array
    {
        if ($app->hasDatabaseConfig()) {
            return $this->getUsersViaDatabase($app);
        }

        if ($app->hasApiConfig()) {
            return $this->getUsersViaApi($app);
        }

        return [];
    }

    // ─── Database Methods ────────────────────────────────────────────

    private function connectDatabase(ClientApp $app): void
    {
        config(['database.connections.' . self::CONNECTION_NAME => $app->getDatabaseConnectionConfig()]);
    }

    /**
     * Clear Spatie permission cache on the remote app's database.
     *
     * When modifying roles/permissions via Direct DB, Spatie's Eloquent events
     * don't fire, so the cached permissions remain stale. This clears the cache
     * entry from the remote app's `cache` table (database cache driver).
     */
    private function clearRemotePermissionCache(): void
    {
        try {
            $db = DB::connection(self::CONNECTION_NAME);

            // Spatie default cache key: 'spatie.permission.cache'
            // Laravel database cache stores with prefix, so we use LIKE to match
            $db->table('cache')
                ->where('key', 'like', '%spatie.permission.cache%')
                ->delete();
        } catch (\Exception $e) {
            // Silently fail — cache will expire naturally
        }
    }

    private function syncUserViaDatabase(ClientApp $app, array $userData): array
    {
        try {
            $this->connectDatabase($app);

            DB::connection(self::CONNECTION_NAME)->table('users')->updateOrInsert(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'is_active' => $userData['is_active'] ?? true,
                    'updated_at' => now(),
                ]
            );

            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => 'User berhasil disinkronkan via database.'];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal sync via DB: ' . $e->getMessage()];
        }
    }

    private function removeUserViaDatabase(ClientApp $app, int $userId): array
    {
        try {
            $this->connectDatabase($app);

            $user = DB::connection(self::CONNECTION_NAME)
                ->table('users')
                ->where('id', $userId)
                ->first();

            if ($user) {
                DB::connection(self::CONNECTION_NAME)
                    ->table('users')
                    ->where('id', $userId)
                    ->update(['is_active' => false, 'updated_at' => now()]);
            }

            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => 'User berhasil dinonaktifkan via database.'];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal remove via DB: ' . $e->getMessage()];
        }
    }

    private function getUsersViaDatabase(ClientApp $app): array
    {
        try {
            $this->connectDatabase($app);

            $users = DB::connection(self::CONNECTION_NAME)
                ->table('users')
                ->select('id', 'name', 'email', 'is_active')
                ->orderBy('name')
                ->get()
                ->toArray();

            DB::disconnect(self::CONNECTION_NAME);

            return array_map(fn ($u) => (array) $u, $users);
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return [];
        }
    }

    // ─── Remote Role & Permission Methods (Direct DB) ─────────────

    public function getRemoteRoles(ClientApp $app): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $roles = $db->table('roles')
                ->select('id', 'name', 'guard_name')
                ->orderBy('name')
                ->get();

            $rolePermissions = $db->table('role_has_permissions as rp')
                ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
                ->select('rp.role_id', 'p.id as permission_id', 'p.name as permission_name')
                ->get()
                ->groupBy('role_id');

            $userCounts = $db->table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->select('role_id', DB::raw('count(*) as count'))
                ->groupBy('role_id')
                ->pluck('count', 'role_id');

            DB::disconnect(self::CONNECTION_NAME);

            return $roles->map(function ($role) use ($rolePermissions, $userCounts) {
                $perms = $rolePermissions->get($role->id, collect());
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'users_count' => $userCounts[$role->id] ?? 0,
                    'permissions' => $perms->map(fn ($p) => [
                        'id' => $p->permission_id,
                        'name' => $p->permission_name,
                    ])->values()->toArray(),
                ];
            })->toArray();
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return [];
        }
    }

    public function getRemotePermissions(ClientApp $app): array
    {
        try {
            $this->connectDatabase($app);

            $permissions = DB::connection(self::CONNECTION_NAME)
                ->table('permissions')
                ->select('id', 'name', 'guard_name')
                ->orderBy('name')
                ->get()
                ->map(fn ($p) => (array) $p)
                ->toArray();

            DB::disconnect(self::CONNECTION_NAME);

            return $permissions;
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return [];
        }
    }

    public function getRemoteUsersWithRoles(ClientApp $app): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $users = $db->table('users')
                ->select('id', 'name', 'email', 'is_active')
                ->orderBy('name')
                ->get();

            $userRoles = $db->table('model_has_roles as mr')
                ->join('roles as r', 'r.id', '=', 'mr.role_id')
                ->where('mr.model_type', 'App\\Models\\User')
                ->select('mr.model_id as user_id', 'r.id as role_id', 'r.name as role_name')
                ->get()
                ->groupBy('user_id');

            DB::disconnect(self::CONNECTION_NAME);

            return $users->map(function ($user) use ($userRoles) {
                $roles = $userRoles->get($user->id, collect());
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => (bool) $user->is_active,
                    'roles' => $roles->map(fn ($r) => [
                        'id' => $r->role_id,
                        'name' => $r->role_name,
                    ])->values()->toArray(),
                ];
            })->toArray();
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return [];
        }
    }

    public function createRemoteRole(ClientApp $app, string $name, array $permissionIds = []): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $exists = $db->table('roles')->where('name', $name)->where('guard_name', 'web')->exists();
            if ($exists) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => "Role '{$name}' sudah ada."];
            }

            $roleId = $db->table('roles')->insertGetId([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($permissionIds)) {
                $pivotData = array_map(fn ($pid) => [
                    'permission_id' => $pid,
                    'role_id' => $roleId,
                ], $permissionIds);
                $db->table('role_has_permissions')->insert($pivotData);
            }

            $this->clearRemotePermissionCache();
            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => "Role '{$name}' berhasil dibuat."];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function updateRemoteRole(ClientApp $app, int $roleId, string $name, array $permissionIds = []): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $exists = $db->table('roles')
                ->where('name', $name)
                ->where('guard_name', 'web')
                ->where('id', '!=', $roleId)
                ->exists();
            if ($exists) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => "Role '{$name}' sudah ada."];
            }

            $db->table('roles')->where('id', $roleId)->update([
                'name' => $name,
                'updated_at' => now(),
            ]);

            $db->table('role_has_permissions')->where('role_id', $roleId)->delete();
            if (!empty($permissionIds)) {
                $pivotData = array_map(fn ($pid) => [
                    'permission_id' => $pid,
                    'role_id' => $roleId,
                ], $permissionIds);
                $db->table('role_has_permissions')->insert($pivotData);
            }

            $this->clearRemotePermissionCache();
            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => "Role '{$name}' berhasil diperbarui."];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function deleteRemoteRole(ClientApp $app, int $roleId): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $role = $db->table('roles')->where('id', $roleId)->first();
            if (!$role) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => 'Role tidak ditemukan.'];
            }

            $userCount = $db->table('model_has_roles')
                ->where('role_id', $roleId)
                ->where('model_type', 'App\\Models\\User')
                ->count();

            if ($userCount > 0) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => "Role '{$role->name}' masih digunakan oleh {$userCount} user."];
            }

            $db->table('role_has_permissions')->where('role_id', $roleId)->delete();
            $db->table('model_has_roles')->where('role_id', $roleId)->delete();
            $db->table('roles')->where('id', $roleId)->delete();

            $this->clearRemotePermissionCache();
            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => "Role '{$role->name}' berhasil dihapus."];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function assignRemoteUserRole(ClientApp $app, int $userId, int $roleId): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $exists = $db->table('model_has_roles')
                ->where('role_id', $roleId)
                ->where('model_id', $userId)
                ->where('model_type', 'App\\Models\\User')
                ->exists();

            if (!$exists) {
                $db->table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ]);
            }

            $this->clearRemotePermissionCache();
            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => 'Role berhasil di-assign.'];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function syncRemoteUserRoles(ClientApp $app, int $userId, array $roleIds): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $db->table('model_has_roles')
                ->where('model_id', $userId)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            if (!empty($roleIds)) {
                $pivotData = array_map(fn ($rid) => [
                    'role_id' => $rid,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ], $roleIds);
                $db->table('model_has_roles')->insert($pivotData);
            }

            $this->clearRemotePermissionCache();
            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => 'Role user berhasil diperbarui.'];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function updateRemoteUser(ClientApp $app, int $userId, array $data): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $user = $db->table('users')->where('id', $userId)->first();
            if (!$user) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => 'User tidak ditemukan.'];
            }

            $emailExists = $db->table('users')
                ->where('email', $data['email'])
                ->where('id', '!=', $userId)
                ->exists();
            if ($emailExists) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => 'Email sudah digunakan oleh user lain.'];
            }

            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'],
                'updated_at' => now(),
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = bcrypt($data['password']);
            }

            $db->table('users')->where('id', $userId)->update($updateData);

            if (isset($data['role_ids'])) {
                $db->table('model_has_roles')
                    ->where('model_id', $userId)
                    ->where('model_type', 'App\\Models\\User')
                    ->delete();

                if (!empty($data['role_ids'])) {
                    $pivotData = array_map(fn ($rid) => [
                        'role_id' => $rid,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ], $data['role_ids']);
                    $db->table('model_has_roles')->insert($pivotData);
                }
            }

            if (!$data['is_active']) {
                $db->table('sessions')->where('user_id', $userId)->delete();
            }

            if (isset($data['role_ids'])) {
                $this->clearRemotePermissionCache();
            }

            DB::disconnect(self::CONNECTION_NAME);

            return ['success' => true, 'message' => "User '{$data['name']}' berhasil diperbarui."];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function toggleRemoteUserActive(ClientApp $app, int $userId): array
    {
        try {
            $this->connectDatabase($app);
            $db = DB::connection(self::CONNECTION_NAME);

            $user = $db->table('users')->where('id', $userId)->first();
            if (!$user) {
                DB::disconnect(self::CONNECTION_NAME);
                return ['success' => false, 'message' => 'User tidak ditemukan.'];
            }

            $newStatus = !$user->is_active;
            $db->table('users')->where('id', $userId)->update([
                'is_active' => $newStatus,
                'updated_at' => now(),
            ]);

            if (!$newStatus) {
                $db->table('sessions')->where('user_id', $userId)->delete();
            }

            DB::disconnect(self::CONNECTION_NAME);

            $statusLabel = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            return ['success' => true, 'message' => "User '{$user->name}' berhasil {$statusLabel}."];
        } catch (\Exception $e) {
            DB::disconnect(self::CONNECTION_NAME);
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    // ─── API Methods ─────────────────────────────────────────────────

    private function buildApiClient(ClientApp $app): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(15)
            ->withHeaders([
                'X-SSO-Secret' => $app->api_secret_key,
                'Accept' => 'application/json',
            ])
            ->baseUrl(rtrim($app->api_base_url, '/'));
    }

    private function syncUserViaApi(ClientApp $app, array $userData): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/users/sync', $userData);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'User berhasil disinkronkan via API.'];
            }

            return ['success' => false, 'message' => 'Gagal sync via API: HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal sync via API: ' . $e->getMessage()];
        }
    }

    private function removeUserViaApi(ClientApp $app, int $userId): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/users/remove', [
                'user_id' => $userId,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'User berhasil dihapus via API.'];
            }

            return ['success' => false, 'message' => 'Gagal remove via API: HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal remove via API: ' . $e->getMessage()];
        }
    }

    private function getUsersViaApi(ClientApp $app): array
    {
        try {
            $response = $this->buildApiClient($app)->get('/sso/users');

            if ($response->successful()) {
                return $response->json('data', []);
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // ─── API Methods for Roles & Permissions ────────────────────────

    public function getRemoteRolesViaApi(ClientApp $app): array
    {
        try {
            $response = $this->buildApiClient($app)->get('/sso/roles');
            if ($response->successful()) {
                return $response->json('data', []);
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getRemotePermissionsViaApi(ClientApp $app): array
    {
        try {
            $response = $this->buildApiClient($app)->get('/sso/permissions');
            if ($response->successful()) {
                return $response->json('data', []);
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getRemoteUsersWithRolesViaApi(ClientApp $app): array
    {
        try {
            $response = $this->buildApiClient($app)->get('/sso/users');
            if ($response->successful()) {
                return $response->json('data', []);
            }
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function createRemoteRoleViaApi(ClientApp $app, string $name, array $permissionNames = []): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/roles/sync', [
                'name' => $name,
                'permissions' => $permissionNames,
            ]);
            if ($response->successful()) {
                return ['success' => true, 'message' => "Role '{$name}' berhasil dibuat via API."];
            }
            return ['success' => false, 'message' => 'Gagal: HTTP ' . $response->status() . ' - ' . ($response->json('message') ?? '')];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function updateRemoteRoleViaApi(ClientApp $app, string $name, array $permissionNames = []): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/roles/sync', [
                'name' => $name,
                'permissions' => $permissionNames,
            ]);
            if ($response->successful()) {
                return ['success' => true, 'message' => "Role '{$name}' berhasil diperbarui via API."];
            }
            return ['success' => false, 'message' => 'Gagal: HTTP ' . $response->status() . ' - ' . ($response->json('message') ?? '')];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function deleteRemoteRoleViaApi(ClientApp $app, string $roleName): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/roles/delete', [
                'name' => $roleName,
            ]);
            if ($response->successful()) {
                return ['success' => true, 'message' => "Role '{$roleName}' berhasil dihapus via API."];
            }
            return ['success' => false, 'message' => 'Gagal: HTTP ' . $response->status() . ' - ' . ($response->json('message') ?? '')];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function syncRemoteUserRolesViaApi(ClientApp $app, string $email, array $roleNames): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/users/sync-roles', [
                'email' => $email,
                'roles' => $roleNames,
            ]);
            if ($response->successful()) {
                return ['success' => true, 'message' => 'Role user berhasil diperbarui via API.'];
            }
            return ['success' => false, 'message' => 'Gagal: HTTP ' . $response->status() . ' - ' . ($response->json('message') ?? '')];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function updateRemoteUserViaApi(ClientApp $app, array $data): array
    {
        try {
            $client = $this->buildApiClient($app);

            // 1. Sync user profile (name, email, is_active)
            $response = $client->post('/sso/users/sync', [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'],
            ]);

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Gagal sync user: HTTP ' . $response->status() . ' - ' . ($response->json('message') ?? '')];
            }

            // 2. Sync roles separately via dedicated endpoint
            if (!empty($data['roles'])) {
                $roleResponse = $client->post('/sso/users/sync-roles', [
                    'email' => $data['email'],
                    'roles' => $data['roles'],
                ]);

                if (!$roleResponse->successful()) {
                    return ['success' => false, 'message' => 'User tersimpan, tapi gagal sync role: HTTP ' . $roleResponse->status() . ' - ' . ($roleResponse->json('message') ?? '')];
                }
            }

            return ['success' => true, 'message' => "User '{$data['name']}' berhasil diperbarui via API."];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }

    public function toggleRemoteUserActiveViaApi(ClientApp $app, string $email, string $name, bool $isActive): array
    {
        try {
            $response = $this->buildApiClient($app)->post('/sso/users/sync', [
                'name' => $name,
                'email' => $email,
                'is_active' => $isActive,
            ]);
            if ($response->successful()) {
                $status = $isActive ? 'diaktifkan' : 'dinonaktifkan';
                return ['success' => true, 'message' => "User berhasil {$status} via API."];
            }
            return ['success' => false, 'message' => 'Gagal: HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
        }
    }
}
