<?php

namespace App\Livewire;

use App\Livewire\Traits\HasNotification;
use App\Models\ClientApp;
use App\Services\RemoteAppService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Kelola Remote App'])]
#[Title('Kelola Remote App')]
class RemoteAppManager extends Component
{
    use HasNotification;

    public ClientApp $app;
    public string $activeTab = 'users';

    // ─── Users Tab ───────────────────────────────────────────────
    public string $userSearch = '';
    public string $userIsActiveFilter = '';
    public bool $userFilterChanged = false;
    public bool $showEditUserModal = false;
    public ?int $editingUserId = null;
    public string $editingUserName = '';
    public string $editingUserEmail = '';
    public string $editingUserPassword = '';
    public string $editingUserPasswordConfirmation = '';
    public bool $editingUserIsActive = true;
    public array $editingUserRoles = [];

    // ─── Roles Tab ───────────────────────────────────────────────
    public string $roleSearch = '';
    public bool $showRoleModal = false;
    public bool $isEditingRole = false;
    public ?int $editingRoleId = null;
    public string $roleName = '';
    public array $rolePermissions = [];

    public bool $showDeleteRoleModal = false;
    public ?int $deletingRoleId = null;
    public string $deletingRoleName = '';

    // ─── Cached remote data ──────────────────────────────────────
    public array $remoteUsers = [];
    public array $remoteRoles = [];
    public array $remotePermissions = [];
    public bool $connectionError = false;
    public string $connectionErrorMessage = '';

    public function mount(ClientApp $app): void
    {
        Gate::authorize('manage', $app);
        $this->app = $app;
        $this->loadRemoteData();
    }

    public function updatedUserSearch(): void
    {
        $this->userFilterChanged = true;
    }

    public function updatedUserIsActiveFilter(): void
    {
        $this->userFilterChanged = true;
    }

    public function resetFilters(): void
    {
        $this->userIsActiveFilter = '';
        $this->userFilterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function loadRemoteData(): void
    {
        $service = app(RemoteAppService::class);

        $this->connectionError = false;
        $this->connectionErrorMessage = '';

        try {
            if ($this->app->hasApiConfig()) {
                $result = $service->testApiConnection($this->app);
                if (!$result['success']) {
                    $this->connectionError = true;
                    $this->connectionErrorMessage = $result['message'];
                    $this->notifyError('Gagal refresh data: ' . $result['message']);
                    return;
                }

                $this->remoteUsers = $service->getRemoteUsersWithRolesViaApi($this->app);
                $this->remoteRoles = $service->getRemoteRolesViaApi($this->app);
                $this->remotePermissions = $service->getRemotePermissionsViaApi($this->app);
                $this->notifySuccess('Data berhasil diperbarui dari API remote.');
            } elseif ($this->app->hasDatabaseConfig()) {
                $result = $service->testDatabaseConnection($this->app);
                if (!$result['success']) {
                    $this->connectionError = true;
                    $this->connectionErrorMessage = $result['message'];
                    $this->notifyError('Gagal refresh data: ' . $result['message']);
                    return;
                }

                $this->remoteUsers = $service->getRemoteUsersWithRoles($this->app);
                $this->remoteRoles = $service->getRemoteRoles($this->app);
                $this->remotePermissions = $service->getRemotePermissions($this->app);
                $this->notifySuccess('Data berhasil diperbarui dari database remote.');
            } else {
                $this->connectionError = true;
                $this->connectionErrorMessage = 'Tidak ada metode sinkronisasi yang dikonfigurasi.';
                $this->notifyError('Tidak ada metode sinkronisasi yang dikonfigurasi.');
            }
        } catch (\Exception $e) {
            $this->connectionError = true;
            $this->connectionErrorMessage = $e->getMessage();
            $this->notifyError('Gagal refresh data: ' . $e->getMessage());
        }
    }

    // ─── Users Tab Actions ───────────────────────────────────────

    public function openEditUserModal(int $userId): void
    {
        Gate::authorize('manage', $this->app);

        $user = collect($this->remoteUsers)->firstWhere('id', $userId);
        if (!$user) return;

        $this->editingUserId = $userId;
        $this->editingUserName = $user['name'];
        $this->editingUserEmail = $user['email'];
        $this->editingUserIsActive = $user['is_active'];
        $this->editingUserPassword = '';
        $this->editingUserPasswordConfirmation = '';
        $this->editingUserRoles = collect($user['roles'])->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->showEditUserModal = true;
    }

    public function saveEditUser(RemoteAppService $service): void
    {
        Gate::authorize('manage', $this->app);

        try {
            $this->validate([
                'editingUserName' => ['required', 'string', 'max:255'],
                'editingUserEmail' => ['required', 'email', 'max:255'],
                'editingUserPassword' => ['nullable', 'string', 'min:8', 'same:editingUserPasswordConfirmation'],
                'editingUserRoles' => ['required', 'array', 'min:1'],
            ], [
                'editingUserName.required' => 'Nama wajib diisi.',
                'editingUserEmail.required' => 'Email wajib diisi.',
                'editingUserEmail.email' => 'Format email tidak valid.',
                'editingUserPassword.min' => 'Password minimal 8 karakter.',
                'editingUserPassword.same' => 'Konfirmasi password tidak cocok.',
                'editingUserRoles.required' => 'Minimal pilih 1 role.',
                'editingUserRoles.min' => 'Minimal pilih 1 role.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        if ($this->app->hasApiConfig()) {
            $roleNames = collect($this->remoteRoles)
                ->whereIn('id', array_map('intval', $this->editingUserRoles))
                ->pluck('name')->toArray();

            $data = [
                'name' => $this->editingUserName,
                'email' => $this->editingUserEmail,
                'is_active' => $this->editingUserIsActive,
                'roles' => $roleNames,
            ];

            $result = $service->updateRemoteUserViaApi($this->app, $data);
        } else {
            $roleIds = array_map('intval', $this->editingUserRoles);
            $data = [
                'name' => $this->editingUserName,
                'email' => $this->editingUserEmail,
                'is_active' => $this->editingUserIsActive,
                'role_ids' => $roleIds,
            ];

            if (!empty($this->editingUserPassword)) {
                $data['password'] = $this->editingUserPassword;
            }

            $result = $service->updateRemoteUser($this->app, $this->editingUserId, $data);
        }

        if ($result['success']) {
            $this->notifySuccess($result['message']);
            $this->loadRemoteData();
            $this->closeEditUserModal();
        } else {
            $this->notifyError($result['message']);
        }
    }

    public function toggleUserActive(int $userId, RemoteAppService $service): void
    {
        Gate::authorize('manage', $this->app);

        if ($this->app->hasApiConfig()) {
            $user = collect($this->remoteUsers)->firstWhere('id', $userId);
            if (!$user) {
                $this->notifyError('User tidak ditemukan.');
                return;
            }
            $result = $service->toggleRemoteUserActiveViaApi(
                $this->app, $user['email'], $user['name'], !$user['is_active']
            );
        } else {
            $result = $service->toggleRemoteUserActive($this->app, $userId);
        }

        if ($result['success']) {
            $this->notifySuccess($result['message']);
            $this->loadRemoteData();
        } else {
            $this->notifyError($result['message']);
        }
    }

    public function closeEditUserModal(): void
    {
        $this->showEditUserModal = false;
        $this->editingUserId = null;
        $this->editingUserName = '';
        $this->editingUserEmail = '';
        $this->editingUserPassword = '';
        $this->editingUserPasswordConfirmation = '';
        $this->editingUserIsActive = true;
        $this->editingUserRoles = [];
        $this->resetValidation();
    }

    // ─── Roles Tab Actions ───────────────────────────────────────

    public function openCreateRoleModal(): void
    {
        Gate::authorize('manage', $this->app);
        $this->resetRoleForm();
        $this->isEditingRole = false;
        $this->showRoleModal = true;
    }

    public function openEditRoleModal(int $roleId): void
    {
        Gate::authorize('manage', $this->app);

        $role = collect($this->remoteRoles)->firstWhere('id', $roleId);
        if (!$role) return;

        $this->editingRoleId = $roleId;
        $this->roleName = $role['name'];
        $this->rolePermissions = collect($role['permissions'])->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->isEditingRole = true;
        $this->showRoleModal = true;
    }

    public function saveRole(RemoteAppService $service): void
    {
        Gate::authorize('manage', $this->app);

        try {
            $this->validate([
                'roleName' => ['required', 'string', 'max:100'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        if ($this->app->hasApiConfig()) {
            $permissionNames = collect($this->remotePermissions)
                ->whereIn('id', array_map('intval', $this->rolePermissions))
                ->pluck('name')->toArray();

            if ($this->isEditingRole) {
                $result = $service->updateRemoteRoleViaApi($this->app, $this->roleName, $permissionNames);
            } else {
                $result = $service->createRemoteRoleViaApi($this->app, $this->roleName, $permissionNames);
            }
        } else {
            $permissionIds = array_map('intval', $this->rolePermissions);

            if ($this->isEditingRole) {
                $result = $service->updateRemoteRole($this->app, $this->editingRoleId, $this->roleName, $permissionIds);
            } else {
                $result = $service->createRemoteRole($this->app, $this->roleName, $permissionIds);
            }
        }

        if ($result['success']) {
            $this->notifySuccess($result['message']);
            $this->loadRemoteData();
            $this->closeRoleModal();
        } else {
            $this->notifyError($result['message']);
        }
    }

    public function confirmDeleteRole(int $roleId): void
    {
        Gate::authorize('manage', $this->app);

        $role = collect($this->remoteRoles)->firstWhere('id', $roleId);
        if (!$role) return;

        $this->deletingRoleId = $roleId;
        $this->deletingRoleName = $role['name'];
        $this->showDeleteRoleModal = true;
    }

    public function deleteRole(RemoteAppService $service): void
    {
        Gate::authorize('manage', $this->app);

        if ($this->app->hasApiConfig()) {
            $result = $service->deleteRemoteRoleViaApi($this->app, $this->deletingRoleName);
        } else {
            $result = $service->deleteRemoteRole($this->app, $this->deletingRoleId);
        }

        if ($result['success']) {
            $this->notifySuccess($result['message']);
            $this->loadRemoteData();
        } else {
            $this->notifyError($result['message']);
        }

        $this->closeDeleteRoleModal();
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->resetRoleForm();
        $this->resetValidation();
    }

    public function closeDeleteRoleModal(): void
    {
        $this->showDeleteRoleModal = false;
        $this->deletingRoleId = null;
        $this->deletingRoleName = '';
    }

    private function resetRoleForm(): void
    {
        $this->editingRoleId = null;
        $this->roleName = '';
        $this->rolePermissions = [];
        $this->isEditingRole = false;
    }

    public function getIsActiveOptionsProperty(): array
    {
        return [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Nonaktif'],
        ];
    }

    // ─── Computed filtered data ──────────────────────────────────

    public function getFilteredUsersProperty(): array
    {
        $users = $this->remoteUsers;

        if ($this->userIsActiveFilter !== '') {
            $isActive = $this->userIsActiveFilter === '1';
            $users = array_values(array_filter($users, fn ($user) => $user['is_active'] === $isActive));
        }

        if (!$this->userSearch) return $users;

        $search = strtolower($this->userSearch);
        return array_values(array_filter($users, function ($user) use ($search) {
            return str_contains(strtolower($user['name']), $search)
                || str_contains(strtolower($user['email']), $search);
        }));
    }

    public function getFilteredRolesProperty(): array
    {
        if (!$this->roleSearch) return $this->remoteRoles;

        $search = strtolower($this->roleSearch);
        return array_values(array_filter($this->remoteRoles, function ($role) use ($search) {
            return str_contains(strtolower($role['name']), $search);
        }));
    }

    /**
     * Group remote permissions by entity prefix.
     *
     * Konvensi: {entity}_{action} — e.g. companies_view, companies_export_excel
     * Entity diambil dengan mencocokkan suffix action yang dikenal,
     * sisanya menjadi nama group.
     */
    public function getPermissionGroupsProperty(): array
    {
        $knownActions = ['view', 'create', 'update', 'delete', 'manage', 'send', 'export_excel', 'export_pdf'];

        $groups = [];
        foreach ($this->remotePermissions as $perm) {
            $name = $perm['name'];
            $entity = $name;

            // Coba cocokkan action suffix terpanjang dulu (export_excel sebelum export)
            usort($knownActions, fn($a, $b) => strlen($b) - strlen($a));
            foreach ($knownActions as $action) {
                $suffix = '_' . $action;
                if (str_ends_with($name, $suffix)) {
                    $entity = substr($name, 0, -strlen($suffix));
                    break;
                }
            }

            $groupName = ucwords(str_replace('_', ' ', $entity));
            $groups[$groupName][] = $perm;
        }

        ksort($groups);
        return $groups;
    }

    public function render()
    {
        Gate::authorize('manage', $this->app);

        $filteredUsers = $this->filteredUsers;

        if ($this->userFilterChanged) {
            $this->notifySuccess("Ditemukan " . count($filteredUsers) . " data user.");
            $this->userFilterChanged = false;
        }

        return view('livewire.remote-app-manager', [
            'filteredUsers' => $filteredUsers,
            'filteredRoles' => $this->filteredRoles,
            'permissionGroups' => $this->permissionGroups,
        ]);
    }
}
