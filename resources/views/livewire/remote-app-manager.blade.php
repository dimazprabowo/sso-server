<div>
    {{-- Header with breadcrumb --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
            <a href="{{ route('client-apps.index') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition" wire:navigate>Client Apps</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $app->name }}</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                    {{ strtoupper(substr($app->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $app->name }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $app->domain }}</span>
                        @if($app->sync_method === 'database')
                            <span class="inline-flex items-center whitespace-nowrap gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 ring-1 ring-violet-500/10">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375"/></svg>
                                Direct DB
                            </span>
                        @elseif($app->sync_method === 'api')
                            <span class="inline-flex items-center whitespace-nowrap gap-1 px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 ring-1 ring-cyan-500/10">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/></svg>
                                API Sync
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <button wire:click="loadRemoteData" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm" wire:loading.attr="disabled" wire:target="loadRemoteData">
                <svg wire:loading.class="hidden" wire:target="loadRemoteData" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                <svg wire:loading wire:target="loadRemoteData" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Refresh Data
            </button>
        </div>
    </div>

    {{-- Connection Error --}}
    @if($connectionError)
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <div>
                    <h4 class="text-sm font-semibold text-red-800 dark:text-red-300">Gagal Terhubung ke Database Remote</h4>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $connectionErrorMessage }}</p>
                    <button wire:click="loadRemoteData" 
                        class="mt-2 text-xs font-medium text-red-700 dark:text-red-300 underline hover:no-underline disabled:opacity-50"
                        wire:loading.attr="disabled"
                        wire:target="loadRemoteData">Coba lagi</button>
                </div>
            </div>
        </div>
    @else

    {{-- Tabs --}}
    <div class="mb-6">
        <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit">
            <button wire:click="$set('activeTab', 'users')" 
                class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $activeTab === 'users' ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }} disabled:opacity-50"
                wire:loading.attr="disabled"
                wire:target="$set('activeTab', 'users')">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    Users
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeTab === 'users' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">{{ count($remoteUsers) }}</span>
                </span>
            </button>
            <button wire:click="$set('activeTab', 'roles')" 
                class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $activeTab === 'roles' ? 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }} disabled:opacity-50"
                wire:loading.attr="disabled"
                wire:target="$set('activeTab', 'roles')">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    Role & Permission
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $activeTab === 'roles' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' }}">{{ count($remoteRoles) }}</span>
                </span>
            </button>
        </div>
    </div>

    {{-- Tab Content --}}
    @if($activeTab === 'users')
        @include('livewire.remote-app-manager.users-tab')
    @elseif($activeTab === 'roles')
        @include('livewire.remote-app-manager.roles-tab')
    @endif

    {{-- Modals --}}
    @include('livewire.remote-app-manager.edit-user-modal')
    @include('livewire.remote-app-manager.role-form-modal')
    @include('livewire.remote-app-manager.role-delete-modal')

    @endif
</div>
