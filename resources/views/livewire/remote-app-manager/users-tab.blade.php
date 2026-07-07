<div>
    {{-- Search & Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3 gap-3">
            <div class="flex items-center flex-1">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input wire:model.live.debounce.300ms="userSearch" type="text" placeholder="Cari user..." class="flex-1 ml-3 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:ring-0 focus:outline-none bg-transparent">
                @if($userSearch)
                    <button wire:click="$set('userSearch', '')"
                        class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition disabled:opacity-50"
                        wire:loading.attr="disabled"
                        wire:target="$set('userSearch', '')">Reset</button>
                @endif
            </div>
            <x-filter-popover :filters="['userIsActiveFilter']">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <x-searchable-select
                        wire:model.live="userIsActiveFilter"
                        :options="$this->isActiveOptions"
                        placeholder="Semua Status"
                        searchPlaceholder="Cari status..."
                    />
                </div>
            </x-filter-popover>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Email</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Roles</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($filteredUsers as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition" wire:key="remote-user-{{ $user['id'] }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($user['name'], 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $user['name'] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate md:hidden">{{ $user['email'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 hidden md:table-cell">
                                <span class="font-mono text-xs">{{ $user['email'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user['roles'] as $role)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold whitespace-nowrap
                                            {{ $role['name'] === 'super admin' ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 ring-1 ring-red-600/10' : ($role['name'] === 'admin' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ring-1 ring-amber-600/10' : 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 ring-1 ring-blue-600/10') }}">
                                            {{ $role['name'] }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleUserActive({{ $user['id'] }})" type="button" 
                                    class="relative inline-flex items-center cursor-pointer group" 
                                    title="Klik untuk toggle status"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleUserActive({{ $user['id'] }})">
                                    <div class="w-11 h-6 rounded-full transition-colors {{ $user['is_active'] ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full transition-all {{ $user['is_active'] ? 'bg-emerald-600 translate-x-5' : 'bg-gray-400 dark:bg-gray-500 translate-x-0' }}"></div>
                                    <svg wire:loading wire:target="toggleUserActive({{ $user['id'] }})" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 animate-spin h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end">
                                    <button wire:click="openEditUserModal({{ $user['id'] }})" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition disabled:opacity-50" 
                                        title="Edit User"
                                        wire:loading.attr="disabled"
                                        wire:target="openEditUserModal({{ $user['id'] }})">
                                        <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openEditUserModal({{ $user['id'] }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        <svg wire:loading wire:target="openEditUserModal({{ $user['id'] }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $userSearch ? 'Tidak ada user yang cocok' : 'Belum ada user di aplikasi ini' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
