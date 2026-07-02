<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar User</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola semua user yang terdaftar di SSO Server</p>
        </div>
        @can('users_create')
            <button wire:click="openCreateModal" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                wire:target="openCreateModal">
                <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openCreateModal" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <svg wire:loading wire:target="openCreateModal" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Tambah User
            </button>
        @endcan
    </div>

    {{-- Search --}}
    @include('livewire.user-manager.search-bar')

    {{-- Table --}}
    @include('livewire.user-manager.user-table', ['users' => $users])

    {{-- Create/Edit Modal --}}
    @include('livewire.user-manager.form-modal')

    {{-- Delete Confirmation Modal --}}
    @include('livewire.user-manager.delete-modal')
</div>
