<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDynamicLike;
use App\Livewire\Traits\HasNotification;
use App\Models\ClientApp;
use App\Models\User;
use App\Services\AppSyncService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Akses Aplikasi'])]
#[Title('Akses Aplikasi')]
class UserAppAccess extends Component
{
    use WithPagination, HasNotification, HasDynamicLike;

    public string $search = '';
    public string $isActiveFilter = '';
    public bool $filterChanged = false;
    public bool $showModal = false;
    public ?int $selectedUserId = null;
    public string $selectedUserName = '';
    public array $assignedApps = [];
    
    public bool $showRevokeModal = false;
    public ?int $revokingUserId = null;
    public string $revokingUserName = '';

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

    public function resetFilters(): void
    {
        $this->isActiveFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function openAssignModal(int $userId): void
    {
        Gate::authorize('user_access_update');

        $user = User::findOrFail($userId);
        $this->selectedUserId = $user->id;
        $this->selectedUserName = $user->name;
        $this->assignedApps = $user->clientApps()->pluck('client_apps.id')->map(fn ($id) => (string) $id)->toArray();
        $this->showModal = true;
    }

    public function saveAccess(): void
    {
        Gate::authorize('user_access_update');

        $user = User::findOrFail($this->selectedUserId);
        $previousAppIds = $user->clientApps()->pluck('client_apps.id')->toArray();
        $newAppIds = array_map('intval', $this->assignedApps);

        $syncData = [];
        foreach ($this->assignedApps as $appId) {
            $syncData[(int) $appId] = [
                'granted_at' => now(),
                'granted_by' => auth()->id(),
            ];
        }

        $user->clientApps()->sync($syncData);

        $syncService = app(AppSyncService::class);

        $granted = array_diff($newAppIds, $previousAppIds);
        foreach ($granted as $appId) {
            $app = ClientApp::find($appId);
            if ($app) {
                $syncService->onAccessGranted($app, $user);
            }
        }

        $revoked = array_diff($previousAppIds, $newAppIds);
        foreach ($revoked as $appId) {
            $app = ClientApp::find($appId);
            if ($app) {
                $syncService->onAccessRevoked($app, $user);
            }
        }

        $this->closeModal();
        $this->notifySuccess("Akses aplikasi untuk {$user->name} berhasil diperbarui.");
    }

    public function confirmRevokeAllAccess(int $userId): void
    {
        Gate::authorize('user_access_update');

        $user = User::findOrFail($userId);
        $this->revokingUserId = $user->id;
        $this->revokingUserName = $user->name;
        $this->showRevokeModal = true;
    }

    public function revokeAllAccess(): void
    {
        Gate::authorize('user_access_update');

        $user = User::findOrFail($this->revokingUserId);
        $apps = $user->clientApps()->get();

        $user->clientApps()->detach();

        $syncService = app(AppSyncService::class);
        foreach ($apps as $app) {
            $syncService->onAccessRevoked($app, $user);
        }

        $this->closeRevokeModal();
        $this->notifySuccess("Semua akses aplikasi untuk {$user->name} telah dicabut.");
    }

    public function closeRevokeModal(): void
    {
        $this->showRevokeModal = false;
        $this->revokingUserId = null;
        $this->revokingUserName = '';
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedUserId = null;
        $this->selectedUserName = '';
        $this->assignedApps = [];
    }

    public function render()
    {
        Gate::authorize('user_access_view');

        $query = User::with(['clientApps', 'roles']);

        if ($this->search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($operator) {
                $q->where('name', $operator, "%{$this->search}%")
                  ->orWhere('email', $operator, "%{$this->search}%");
            });
        }

        if ($this->isActiveFilter !== '') {
            $query->where('is_active', $this->isActiveFilter === '1');
        }

        $users = $query->orderBy('name')->paginate(15);
        $allApps = ClientApp::where('is_active', true)->orderBy('name')->get();

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$users->total()} data user.");
            $this->filterChanged = false;
        }

        return view('livewire.user-app-access', compact('users', 'allApps'));
    }
}
