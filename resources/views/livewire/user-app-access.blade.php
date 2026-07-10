<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Akses User ke Aplikasi</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola aplikasi mana saja yang bisa diakses oleh setiap user</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center px-4 py-3 gap-3">
            <div class="flex items-center flex-1">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..." class="flex-1 ml-3 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:ring-0 focus:outline-none bg-transparent">
                @if($search)
                    <button wire:click="$set('search', '')"
                        class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition disabled:opacity-50"
                        wire:loading.attr="disabled"
                        wire:target="$set('search', '')">Reset</button>
                @endif
            </div>
            <x-filter-popover :filters="['isActiveFilter']">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <x-searchable-select
                        wire:model.live="isActiveFilter"
                        :options="$this->isActiveOptions"
                        placeholder="Semua Status"
                        searchPlaceholder="Cari status..."
                    />
                </div>
            </x-filter-popover>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Role</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aplikasi Diakses</th>
                        <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition" wire:key="access-{{ $user->id }}">
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
                                            <span wire:key="access-role-{{ $user->id }}-{{ $role->id }}" class="inline-flex items-center whitespace-nowrap px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/10">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->hasRole('super-admin'))
                                    <span class="inline-flex items-center whitespace-nowrap gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ring-1 ring-amber-600/10">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Semua Aplikasi
                                    </span>
                                @elseif($user->clientApps->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($user->clientApps->take(3) as $app)
                                            <span class="inline-flex items-center whitespace-nowrap px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                {{ $app->name }}
                                            </span>
                                        @endforeach
                                        @if($user->clientApps->count() > 3)
                                            <span class="inline-flex items-center whitespace-nowrap px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                +{{ $user->clientApps->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Belum ada akses</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @can('user_access_update')
                                    @unless($user->hasRole('super-admin'))
                                        <div class="flex items-center justify-end gap-1">
                                            <button wire:click="openAssignModal({{ $user->id }})" 
                                                class="p-2 rounded-lg text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition disabled:opacity-50" 
                                                title="Atur Akses"
                                                wire:loading.attr="disabled"
                                                wire:target="openAssignModal({{ $user->id }})">
                                                <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openAssignModal({{ $user->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <svg wire:loading wire:target="openAssignModal({{ $user->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            </button>
                                            @if($user->clientApps->isNotEmpty())
                                                <button wire:click="confirmRevokeAllAccess({{ $user->id }})" 
                                                    class="p-2 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition disabled:opacity-50" 
                                                    title="Cabut Semua Akses"
                                                    wire:loading.attr="disabled"
                                                    wire:target="confirmRevokeAllAccess({{ $user->id }})">
                                                    <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="confirmRevokeAllAccess({{ $user->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    <svg wire:loading wire:target="confirmRevokeAllAccess({{ $user->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Auto-access</span>
                                    @endunless
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada user ditemukan</p>
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

    {{-- Assign Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md z-10">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Atur Akses Aplikasi</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $selectedUserName }}</p>
                        </div>
                        <x-cancel-button icon wire:click="closeModal" target="closeModal" />
                    </div>

                    <div class="p-6">
                        @if($allApps->isEmpty())
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada aplikasi aktif yang terdaftar.</p>
                        @else
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                @foreach($allApps as $app)
                                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-600 hover:bg-emerald-100/30 dark:hover:bg-emerald-900/20 transition cursor-pointer">
                                        <input type="checkbox" wire:model="assignedApps" value="{{ $app->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500/40 bg-white dark:bg-gray-800 transition">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $app->name }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $app->domain }}</p>
                                        </div>
                                        <span class="inline-flex items-center whitespace-nowrap gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $app->is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $app->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ $app->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                            <x-cancel-button wire:click="closeModal" target="closeModal" />
                            <button wire:click="saveAccess" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                                <svg wire:loading wire:target="saveAccess" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Simpan Akses
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Revoke Confirmation Modal --}}
    @if($showRevokeModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeRevokeModal"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-sm z-10 p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Cabut Semua Akses</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Yakin ingin mencabut semua akses aplikasi untuk <span class="font-semibold text-gray-700 dark:text-white">{{ $revokingUserName }}</span>? User tidak akan bisa mengakses aplikasi apapun.</p>
                    <div class="flex items-center justify-center gap-3">
                        <x-cancel-button wire:click="closeRevokeModal" target="closeRevokeModal" />
                        <button wire:click="revokeAllAccess" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <svg wire:loading wire:target="revokeAllAccess" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Ya, Cabut Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
