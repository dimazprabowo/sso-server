@if($showRegenerateModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeRegenerateModal"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm z-10 p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Regenerate Client Secret?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Anda akan membuat ulang Client Secret untuk:</p>
                <p class="text-sm font-semibold text-gray-700 dark:text-white bg-gray-50 dark:bg-gray-700/50 rounded-lg px-3 py-2 mb-4">{{ $regeneratingAppName }}</p>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-3 mb-6">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <p class="text-xs text-red-600 dark:text-red-400 text-left leading-relaxed">
                            <span class="font-semibold">Perhatian:</span> Secret lama akan langsung tidak berlaku. Semua koneksi OAuth yang menggunakan secret lama akan terputus dan perlu diperbarui.
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <x-cancel-button wire:click="closeRegenerateModal" target="closeRegenerateModal" />
                    <button wire:click="regenerateSecret" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                        <svg wire:loading wire:target="regenerateSecret" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <svg wire:loading.class="hidden" wire:target="regenerateSecret" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                        Ya, Regenerate
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
