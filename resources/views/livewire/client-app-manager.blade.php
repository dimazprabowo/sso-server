<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Client Applications</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola aplikasi client yang terhubung ke SSO Server</p>
        </div>
        @can('client_apps_create')
            <button wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                wire:target="openCreateModal">
                <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openCreateModal" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <svg wire:loading wire:target="openCreateModal" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Tambah Aplikasi
            </button>
        @endcan
    </div>

    {{-- Search --}}
    @include('livewire.client-app-manager.search-bar')

    {{-- Table --}}
    @include('livewire.client-app-manager.app-table', ['apps' => $apps])

    {{-- Create/Edit Modal --}}
    @include('livewire.client-app-manager.form-modal')

    {{-- Delete Modal --}}
    @include('livewire.client-app-manager.delete-modal')

    {{-- Regenerate Secret Confirmation Modal --}}
    @include('livewire.client-app-manager.regenerate-modal')

    {{-- Secret Reveal Modal --}}
    @include('livewire.client-app-manager.secret-modal')
</div>
