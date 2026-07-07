<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aplikasi</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Domain</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Client ID</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Users</th>
                    <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($apps as $app)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition" wire:key="app-{{ $app->id }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold shadow-sm shrink-0">
                                    {{ strtoupper(substr($app->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $app->name }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $app->redirect_uri }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 hidden md:table-cell">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $app->is_active ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-500' }}"></span>
                                {{ $app->domain }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-md font-mono">{{ Str::limit($app->oauth_client_id, 16) }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @can('client_apps_update')
                                <button wire:click="toggleActive({{ $app->id }})" type="button" 
                                    class="relative inline-flex items-center cursor-pointer group" 
                                    title="Klik untuk toggle status"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleActive({{ $app->id }})">
                                    <div class="w-11 h-6 rounded-full transition-colors {{ $app->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full transition-all {{ $app->is_active ? 'bg-emerald-600 translate-x-5' : 'bg-gray-400 dark:bg-gray-500 translate-x-0' }}"></div>
                                    <svg wire:loading wire:target="toggleActive({{ $app->id }})" class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 animate-spin h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </button>
                            @else
                                <div class="relative inline-flex items-center opacity-60">
                                    <div class="w-11 h-6 rounded-full {{ $app->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 rounded-full {{ $app->is_active ? 'bg-emerald-600 translate-x-5' : 'bg-gray-400 dark:bg-gray-500 translate-x-0' }}"></div>
                                </div>
                            @endcan
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 hidden lg:table-cell">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center whitespace-nowrap gap-1 px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-md text-xs font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    {{ $app->users_count ?? $app->users()->count() }}
                                </span>
                                @if($app->sync_method && $app->sync_method !== 'none')
                                    <span class="inline-flex items-center whitespace-nowrap gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold
                                        {{ $app->sync_method === 'database' ? 'bg-violet-50 text-violet-600 ring-1 ring-violet-500/10' : 'bg-cyan-50 text-cyan-600 ring-1 ring-cyan-500/10' }}"
                                        title="Sync: {{ $app->sync_method }}">
                                        {{ $app->sync_method === 'database' ? 'DB' : 'API' }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                @can('manage', $app)
                                    @if($app->hasDatabaseConfig() || $app->hasApiConfig())
                                        <a href="{{ route('client-apps.manage', $app) }}" 
                                            x-data="{ loading: false }"
                                            @click="loading = true"
                                            class="p-2 rounded-lg text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/30 transition" 
                                            title="Kelola User & Role" 
                                            wire:navigate>
                                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        </a>
                                    @endif
                                @endcan
                                @can('regenerateSecret', $app)
                                    <button wire:click="confirmRegenerate({{ $app->id }})" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition disabled:opacity-50" 
                                        title="Regenerate Secret"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmRegenerate({{ $app->id }})">
                                        <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="confirmRegenerate({{ $app->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25z"/></svg>
                                        <svg wire:loading wire:target="confirmRegenerate({{ $app->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                @endcan
                                @can('client_apps_update')
                                    <button wire:click="openEditModal({{ $app->id }})" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition disabled:opacity-50" 
                                        title="Edit"
                                        wire:loading.attr="disabled"
                                        wire:target="openEditModal({{ $app->id }})">
                                        <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="openEditModal({{ $app->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        <svg wire:loading wire:target="openEditModal({{ $app->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                @endcan
                                @can('client_apps_delete')
                                    <button wire:click="confirmDelete({{ $app->id }})" 
                                        class="p-2 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition disabled:opacity-50" 
                                        title="Hapus"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmDelete({{ $app->id }})">
                                        <svg wire:loading.class.remove="inline-block" wire:loading.class.add="hidden" wire:target="confirmDelete({{ $app->id }})" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        <svg wire:loading wire:target="confirmDelete({{ $app->id }})" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/></svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada aplikasi terdaftar</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tambahkan aplikasi client untuk memulai</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($apps->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $apps->links() }}
        </div>
    @endif
</div>
