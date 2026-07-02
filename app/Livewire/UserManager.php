<?php

namespace App\Livewire;

use App\Livewire\Traits\HasNotification;
use App\Models\User;
use App\Services\AppSyncService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app', ['title' => 'Kelola User'])]
#[Title('Kelola User')]
class UserManager extends Component
{
    use WithPagination, HasNotification;

    public string $search = '';
    public string $isActiveFilter = '';
    public bool $filterChanged = false;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditing = false;

    public ?int $editingUserId = null;
    public ?int $deletingUserId = null;
    public string $deletingUserName = '';

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $is_active = true;
    public array $selectedRoles = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatedIsActiveFilter(): void
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function getIsActiveOptionsProperty(): array
    {
        return [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Nonaktif'],
        ];
    }

    public function openCreateModal(): void
    {
        Gate::authorize('create', User::class);
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('update', $user);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_active = $user->is_active;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->password = '';
        $this->password_confirmation = '';
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(): void
    {

        if ($this->isEditing) {
            $this->update();
        } else {
            $this->store();
        }
    }

    private function store(): void
    {
        Gate::authorize('create', User::class);

        try {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'selectedRoles' => ['required', 'array', 'min:1'],
                'selectedRoles.*' => ['required', 'string', 'exists:roles,name'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'is_active' => $this->is_active,
        ]);

        $user->syncRoles($this->selectedRoles);

        app(AppSyncService::class)->syncUserToApps($user);

        $this->closeModal();
        $this->notifySuccess('User berhasil ditambahkan.');
    }

    private function update(): void
    {
        Gate::authorize('update', User::findOrFail($this->editingUserId));

        try {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingUserId)],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'selectedRoles' => ['required', 'array', 'min:1'],
                'selectedRoles.*' => ['required', 'string', 'exists:roles,name'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        $user = User::findOrFail($this->editingUserId);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->is_active = $this->is_active;

        if (! empty($this->password)) {
            $user->password = $this->password;
        }

        $user->save();
        $user->syncRoles($this->selectedRoles);

        app(AppSyncService::class)->syncUserToApps($user->fresh());

        if ($user->id === auth()->id()) {
            $this->dispatch('profile-updated');
        }

        $this->closeModal();
        $this->notifySuccess('User berhasil diperbarui.');
    }

    public function confirmDelete(int $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('delete', $user);
        $this->deletingUserId = $user->id;
        $this->deletingUserName = $user->name;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        $user = User::findOrFail($this->deletingUserId);
        Gate::authorize('delete', $user);

        app(AppSyncService::class)->removeUserFromApps($user);

        $user->tokens()->delete();
        $user->loginHistories()->delete();
        $user->clientApps()->detach();
        $user->delete();

        $this->showDeleteModal = false;
        $this->deletingUserId = null;
        $this->deletingUserName = '';
        $this->notifySuccess('User berhasil dihapus.');
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        Gate::authorize('toggleActive', $user);
        $user->is_active = ! $user->is_active;
        $user->save();

        app(AppSyncService::class)->syncUserToApps($user);

        if ($user->id === auth()->id()) {
            $this->dispatch('profile-updated');
        }

        $this->notifySuccess("Status user {$user->name} berhasil diubah.");
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_active = true;
        $this->selectedRoles = [];
    }

    public function render()
    {
        Gate::authorize('viewAny', User::class);

        $query = User::with('roles');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->search}%")
                  ->orWhere('email', 'ilike', "%{$this->search}%");
            });
        }

        if ($this->isActiveFilter !== '') {
            $query->where('is_active', $this->isActiveFilter === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::orderBy('name')->get();

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$users->total()} data user.");
            $this->filterChanged = false;
        }

        return view('livewire.user-manager', compact('users', 'roles'));
    }
}
