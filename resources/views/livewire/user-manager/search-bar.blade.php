<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3 gap-3">
        <div class="flex items-center flex-1">
            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..." class="flex-1 ml-3 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:ring-0 focus:outline-none bg-transparent">
            @if($search)
                <button wire:click="$set('search', '')" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition">Reset</button>
            @endif
        </div>
        <div class="sm:w-44">
            <x-searchable-select
                wire:model.live="isActiveFilter"
                :options="$this->isActiveOptions"
                placeholder="Semua Status"
                searchPlaceholder="Cari status..."
            />
        </div>
    </div>
</div>
