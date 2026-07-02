<?php

namespace App\Livewire;

use App\Livewire\Traits\HasNotification;
use App\Services\RolePermissionService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app', ['title' => 'Role & Permission'])]
#[Title('Role & Permission')]
class RolePermissionManager extends Component
{
    use HasNotification;

    public string $search = '';

    public bool $showRoleModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditing = false;

    public ?int $editingRoleId = null;
    public ?int $deletingRoleId = null;
    public string $deletingRoleName = '';

    public string $roleName = '';
    public array $selectedPermissions = [];

    public function openCreateModal(): void
    {
        Gate::authorize('create', Role::class);
        $this->resetForm();
        $this->isEditing = false;
        $this->showRoleModal = true;
    }

    public function openEditModal(int $roleId): void
    {
        $role = Role::findOrFail($roleId);
        Gate::authorize('update', $role);
        $this->editingRoleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
        $this->showRoleModal = true;
    }

    public function save(): void
    {
        try {
            $this->validate([
                'roleName' => ['required', 'string', 'max:255'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        if ($this->isEditing) {
            $role = Role::findOrFail($this->editingRoleId);
            Gate::authorize('update', $role);

            $role->update(['name' => $this->roleName]);
            $role->syncPermissions($this->selectedPermissions);

            $this->notifySuccess("Role {$role->name} berhasil diperbarui.");
        } else {
            Gate::authorize('create', Role::class);

            $role = Role::create(['name' => $this->roleName, 'guard_name' => 'web']);
            $role->syncPermissions($this->selectedPermissions);

            $this->notifySuccess("Role {$role->name} berhasil ditambahkan.");
        }

        $this->closeModal();
    }

    public function confirmDelete(int $roleId): void
    {
        $role = Role::findOrFail($roleId);
        Gate::authorize('delete', $role);
        $this->deletingRoleId = $role->id;
        $this->deletingRoleName = $role->name;
        $this->showDeleteModal = true;
    }

    public function deleteRole(): void
    {
        $role = Role::findOrFail($this->deletingRoleId);
        Gate::authorize('delete', $role);

        $roleName = $role->name;
        $role->delete();

        $this->showDeleteModal = false;
        $this->deletingRoleId = null;
        $this->deletingRoleName = '';
        $this->notifySuccess('Role berhasil dihapus.');
    }

    public function closeModal(): void
    {
        $this->showRoleModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->editingRoleId = null;
        $this->roleName = '';
        $this->selectedPermissions = [];
    }

    public function render(RolePermissionService $service)
    {
        Gate::authorize('viewAny', Role::class);

        $roles = Role::with('permissions')
            ->when($this->search, fn($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        $permissionGroups = $service->buildPermissionGroups();
        $permissionLabels = $service->getPermissionLabels();

        return view('livewire.role-permission-manager', compact('roles', 'permissionGroups', 'permissionLabels'));
    }
}
