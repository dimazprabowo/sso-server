@if($showRoleModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeRoleModal"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">{{ $isEditingRole ? 'Edit Role' : 'Tambah Role Baru' }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $app->name }} — Remote Database</p>
                    </div>
                    <x-cancel-button icon wire:click="closeRoleModal" target="closeRoleModal" />
                </div>

                {{-- Body --}}
                <form wire:submit="saveRole" class="p-6 space-y-5 max-h-[65vh] overflow-y-auto">
                    {{-- Role Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Role <span class="text-red-500">*</span></label>
                        <input wire:model="roleName" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm" placeholder="Contoh: manager">
                        @error('roleName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Permissions --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</label>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="$set('rolePermissions', {{ json_encode(collect($remotePermissions)->pluck('id')->map(fn($id) => (string) $id)->toArray()) }})" class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition">Pilih Semua</button>
                                <span class="text-gray-300 dark:text-gray-600">|</span>
                                <button type="button" wire:click="$set('rolePermissions', [])" class="text-[11px] font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">Hapus Semua</button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($permissionGroups as $groupName => $permissions)
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ $groupName }}</h5>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ count($permissions) }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($permissions as $perm)
                                            <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer transition hover:bg-white dark:hover:bg-gray-800" wire:key="role-perm-{{ $perm['id'] }}">
                                                <input type="checkbox" value="{{ $perm['id'] }}" wire:model="rolePermissions" class="w-3.5 h-3.5 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500/40 dark:bg-gray-700">
                                                <span class="text-xs text-gray-700 dark:text-gray-300">{{ $perm['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ count($rolePermissions) }} permission{{ count($rolePermissions) !== 1 ? 's' : '' }} dipilih
                        </p>
                        <div class="flex items-center gap-3">
                            <x-cancel-button wire:click="closeRoleModal" target="closeRoleModal" />
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed" wire:target="saveRole">
                                <svg wire:loading wire:target="saveRole" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                {{ $isEditingRole ? 'Simpan Perubahan' : 'Buat Role' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
