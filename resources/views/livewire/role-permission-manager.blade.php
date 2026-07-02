<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Role & Permission</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola role dan hak akses pengguna</p>
        </div>
        @can('roles_create')
            <button wire:click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-sm text-sm transition disabled:opacity-70 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    wire:target="openCreateModal">
                <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openCreateModal" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <svg wire:loading wire:target="openCreateModal" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Tambah Role
            </button>
        @endcan
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex items-center px-4 py-3">
            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari role..." class="flex-1 ml-3 text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 dark:placeholder-gray-500 border-0 focus:ring-0 focus:outline-none bg-transparent">
            @if($search)
                <button wire:click="$set('search', '')" 
                    class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="$set('search', '')">Reset</button>
            @endif
        </div>
    </div>

    {{-- Roles Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg {{ $role->name === 'super-admin' ? 'bg-amber-50 dark:bg-amber-900/30' : 'bg-emerald-50 dark:bg-emerald-900/30' }} flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 {{ $role->name === 'super-admin' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-white">{{ $role->name }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $role->guard_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($role->permissions as $perm)
                                        <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                            {{ $permissionLabels[$perm->name] ?? $perm->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada permission</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $role->users()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($role->name !== 'super-admin')
                                    <div class="flex items-center justify-end gap-2">
                                        @can('roles_update')
                                            <button wire:click="openEditModal({{ $role->id }})"
                                                    class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition disabled:opacity-50"
                                                    wire:loading.attr="disabled"
                                                    wire:target="openEditModal({{ $role->id }})">
                                                <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openEditModal({{ $role->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                <svg wire:loading wire:target="openEditModal({{ $role->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            </button>
                                        @endcan
                                        @can('roles_delete')
                                            <button wire:click="confirmDelete({{ $role->id }})"
                                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition disabled:opacity-50"
                                                    wire:loading.attr="disabled"
                                                    wire:target="confirmDelete({{ $role->id }})">
                                                <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="confirmDelete({{ $role->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                <svg wire:loading wire:target="confirmDelete({{ $role->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                            </button>
                                        @endcan
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Protected</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Tidak ada role ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Role Modal --}}
    @if($showRoleModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-xl">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                        {{ $isEditing ? 'Edit Role' : 'Tambah Role' }}
                    </h3>
                </div>
                <form wire:submit="save" class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Role <span class="text-red-500">*</span></label>
                        <input wire:model="roleName" type="text" placeholder="Contoh: editor"
                               class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm">
                        @error('roleName')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                        <div class="max-h-64 overflow-y-auto p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                            @foreach($permissionGroups as $groupName => $groupPerms)
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $groupName }}</span>
                                        <div class="flex-1 border-t border-gray-200 dark:border-gray-700"></div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pl-1">
                                        @foreach($groupPerms as $perm)
                                            <label class="flex items-center gap-2 cursor-pointer py-1 px-2 rounded-lg hover:bg-white dark:hover:bg-gray-800 transition">
                                                <input type="checkbox" wire:model="selectedPermissions" value="{{ $perm['name'] }}"
                                                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500/40 bg-white dark:bg-gray-800">
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $perm['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <x-cancel-button wire:click="closeModal" target="closeModal" variant="bordered" />
                        <button type="submit"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-sm text-sm transition inline-flex items-center gap-2"
                                wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeModal"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Hapus Role</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus role <strong class="text-gray-800 dark:text-white">{{ $deletingRoleName }}</strong>?</p>
                <div class="flex justify-center gap-3">
                    <x-cancel-button wire:click="closeModal" target="closeModal" variant="bordered" />
                    <button wire:click="deleteRole"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                        <svg wire:loading wire:target="deleteRole" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
