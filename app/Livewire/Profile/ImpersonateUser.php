<?php

namespace App\Livewire\Profile;

use App\Livewire\Traits\HasDynamicLike;
use App\Livewire\Traits\HasNotification;
use App\Models\User;
use App\Services\ImpersonateService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app', ['title' => 'Profil'])]
#[Title('Profil')]
class ImpersonateUser extends Component
{
    use WithPagination, AuthorizesRequests, HasNotification, HasDynamicLike;

    protected $paginationTheme = 'tailwind';

    public string $search = '';
    public string $roleFilter = '';
    public string $isActiveFilter = '';
    public bool $filterChanged = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatedRoleFilter(): void
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

    public function getRoleOptionsProperty(): array
    {
        return Role::orderBy('name')->get()
            ->map(fn ($role) => ['value' => $role->name, 'label' => ucfirst($role->name)])
            ->toArray();
    }

    public function resetFilters(): void
    {
        $this->roleFilter = '';
        $this->isActiveFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function startImpersonate(int $userId, ImpersonateService $service): void
    {
        abort_unless(auth()->user()->can('users_impersonate'), 403);
        abort_if($service->isImpersonating(), 403, 'Anda sedang dalam sesi impersonate.');

        $target = User::findOrFail($userId);
        $this->authorize('impersonate', $target);

        $service->start($target);

        $this->notifySuccess("Anda sekarang beraksi sebagai {$target->name}.");
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        abort_unless(auth()->user()->can('users_impersonate'), 403);

        $query = User::with('roles')
            ->where('id', '!=', auth()->id());

        if ($this->search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($operator) {
                $q->where('name', $operator, "%{$this->search}%")
                  ->orWhere('email', $operator, "%{$this->search}%");
            });
        }

        if ($this->roleFilter) {
            $query->role($this->roleFilter);
        }

        if ($this->isActiveFilter !== '') {
            $query->where('is_active', $this->isActiveFilter === '1');
        }

        $users = $query->orderBy('name')->paginate(8);

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$users->total()} data user.");
            $this->filterChanged = false;
        }

        return view('livewire.profile.impersonate-user', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
