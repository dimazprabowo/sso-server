@if($showDeleteRoleModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeDeleteRoleModal"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-2">Hapus Role?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Apakah Anda yakin ingin menghapus role <strong class="text-gray-700 dark:text-gray-200">{{ $deletingRoleName }}</strong> dari <strong>{{ $app->name }}</strong>?
                    </p>
                    <p class="text-xs text-red-500 dark:text-red-400 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="flex items-center gap-3 px-6 pb-6">
                    <x-cancel-button wire:click="closeDeleteRoleModal" target="closeDeleteRoleModal" variant="muted" class="flex-1 justify-center" />
                    <button wire:click="deleteRole" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed" wire:target="deleteRole">
                        <svg wire:loading wire:target="deleteRole" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Hapus Role
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
