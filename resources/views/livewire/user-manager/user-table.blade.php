<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Role</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Bergabung</th>
                    <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition" wire:key="user-{{ $user->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold shadow-sm shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            @if($user->roles->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span wire:key="user-role-{{ $user->id }}-{{ $role->id }}" class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $role->name === 'super-admin' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ring-1 ring-amber-600/10' : ($role->name === 'admin' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/10' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 ring-1 ring-gray-300/50 dark:ring-gray-600') }}">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @can('users_update')
                                @if($user->id === auth()->id())
                                    <div class="relative inline-flex items-center cursor-not-allowed opacity-60">
                                        <div class="w-11 h-6 bg-emerald-100 dark:bg-emerald-900/30 rounded-full"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-emerald-600 rounded-full transition-transform translate-x-5"></div>
                                    </div>
                                @else
                                    <button wire:click="toggleActive({{ $user->id }})" type="button" 
                                        class="relative inline-flex items-center cursor-pointer group" 
                                        title="Klik untuk toggle status"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleActive({{ $user->id }})">
                                        <div class="w-11 h-6 rounded-full transition-colors {{ $user->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full transition-all {{ $user->is_active ? 'bg-emerald-600 translate-x-5' : 'bg-gray-400 dark:bg-gray-500 translate-x-0' }}"></div>
                                        <svg wire:loading wire:target="toggleActive({{ $user->id }})" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 animate-spin h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </button>
                                @endif
                            @else
                                <div class="relative inline-flex items-center opacity-60">
                                    <div class="w-11 h-6 rounded-full {{ $user->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full {{ $user->is_active ? 'bg-emerald-600 translate-x-5' : 'bg-gray-400 dark:bg-gray-500 translate-x-0' }}"></div>
                                </div>
                            @endcan
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500 dark:text-gray-400 hidden lg:table-cell">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                @can('users_update')
                                    <button wire:click="openEditModal({{ $user->id }})" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition disabled:opacity-50" 
                                        title="Edit"
                                        wire:loading.attr="disabled"
                                        wire:target="openEditModal({{ $user->id }})">
                                        <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openEditModal({{ $user->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        <svg wire:loading wire:target="openEditModal({{ $user->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                @endcan
                                @can('users_delete')
                                    @if($user->id !== auth()->id())
                                        <button wire:click="confirmDelete({{ $user->id }})" 
                                            class="p-2 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition disabled:opacity-50" 
                                            title="Hapus"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDelete({{ $user->id }})">
                                            <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="confirmDelete({{ $user->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            <svg wire:loading wire:target="confirmDelete({{ $user->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada user ditemukan</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Coba ubah kata kunci pencarian</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    @endif
</div>
