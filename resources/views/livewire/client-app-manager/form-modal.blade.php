@if($showModal)
    <div class="fixed inset-0 z-[60] overflow-y-auto" x-data="{ activeTab: 'general' }" x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="closeModal"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">{{ $isEditing ? 'Edit Aplikasi' : 'Tambah Aplikasi Baru' }}</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $isEditing ? 'Perbarui konfigurasi aplikasi client' : 'Daftarkan aplikasi client baru ke SSO' }}</p>
                    </div>
                    <x-cancel-button icon wire:click="closeModal" target="closeModal" />
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-gray-100 dark:border-gray-700 px-6">
                    <button type="button" @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="px-4 py-3 text-sm font-medium border-b-2 transition -mb-px">
                        Umum
                    </button>
                    <button type="button" @click="activeTab = 'remote'" :class="activeTab === 'remote' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'" class="px-4 py-3 text-sm font-medium border-b-2 transition -mb-px">
                        Konfigurasi Remote
                    </button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit="save" class="p-6 space-y-5 max-h-[65vh] overflow-y-auto">

                    {{-- Tab: General --}}
                    <div x-show="activeTab === 'general'" x-cloak class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Aplikasi <span class="text-red-500">*</span></label>
                            <input wire:model="name" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm" placeholder="Contoh: Helpdesk IT">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Nama aplikasi untuk identifikasi, maksimal 255 karakter.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Domain <span class="text-red-500">*</span></label>
                            <input wire:model="domain" type="text" class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm" placeholder="Contoh: https://helpdesk.company.com">
                            @error('domain') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">contoh: <code class="text-emerald-600 dark:text-emerald-400">https://helpdesk.company.com</code></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Redirect URI (Callback) <span class="text-red-500">*</span></label>
                            <input wire:model="redirect_uri" type="url" class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono text-xs" placeholder="https://helpdesk.company.com/auth/callback">
                            @error('redirect_uri') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">contoh: <code class="text-emerald-600 dark:text-emerald-400">https://helpdesk.company.com/auth/callback</code></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Post-Logout Redirect URI <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional)</span></label>
                            <input wire:model="post_logout_redirect_uri" type="url" class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono text-xs" placeholder="https://helpdesk.company.com/login">
                            @error('post_logout_redirect_uri') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">contoh: <code class="text-emerald-600 dark:text-emerald-400">https://helpdesk.company.com/login</code></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional)</span></label>
                            <textarea wire:model="description" rows="2" class="w-full px-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm resize-none" placeholder="Deskripsi singkat aplikasi..."></textarea>
                            @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Deskripsi singkat tentang aplikasi, maksimal 1000 karakter.</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/40 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-gray-500 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Aplikasi aktif</span>
                        </div>
                    </div>

                    {{-- Tab: Remote Config --}}
                    <div x-show="activeTab === 'remote'" x-cloak class="space-y-5" x-data="{ showDbPass: false, showApiKey: false, showGuide: false }">
                        {{-- Sync Method --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Metode Sinkronisasi <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-2" x-data="{ method: $wire.entangle('sync_method') }">
                                {{-- None --}}
                                <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                       :class="method === 'none' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                                    <input type="radio" wire:model.live="sync_method" value="none" class="sr-only">
                                    <svg class="w-5 h-5" :class="method === 'none' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400'" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                    <span class="text-xs font-semibold" :class="method === 'none' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400'">OAuth Only</span>
                                </label>
                                {{-- API --}}
                                <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                       :class="method === 'api' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                                    <input type="radio" wire:model.live="sync_method" value="api" class="sr-only">
                                    <svg class="w-5 h-5" :class="method === 'api' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400'" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.556a4.5 4.5 0 00-1.242-7.244l4.5-4.5a4.5 4.5 0 016.364 6.364l-1.757 1.757"/></svg>
                                    <span class="text-xs font-semibold" :class="method === 'api' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400'">API Sync</span>
                                </label>
                                {{-- Database --}}
                                <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                       :class="method === 'database' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">
                                    <input type="radio" wire:model.live="sync_method" value="database" class="sr-only">
                                    <svg class="w-5 h-5" :class="method === 'database' ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400'" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375"/></svg>
                                    <span class="text-xs font-semibold" :class="method === 'database' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400'">Direct DB</span>
                                </label>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                @error('sync_method') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                <button type="button" @click="showGuide = true" class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                    Panduan Setup
                                </button>
                            </div>
                        </div>

                        {{-- Guide Modal (Alpine.js only) --}}
                        <template x-teleport="body">
                            <div x-show="showGuide" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center p-4">
                                <div class="fixed inset-0 bg-black/50" @click="showGuide = false"></div>
                                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] flex flex-col" @click.stop>
                                    {{-- Guide Header --}}
                                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Panduan Metode Sinkronisasi</h3>
                                                <p class="text-[11px] text-gray-400 dark:text-gray-500">Pilih metode yang sesuai dengan kebutuhan</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="showGuide = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    {{-- Guide Body (scrollable) --}}
                                    <div class="overflow-y-auto p-6 space-y-6 text-sm text-gray-600 dark:text-gray-300">

                                        {{-- OAuth Only --}}
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                                </span>
                                                <h4 class="font-semibold text-gray-800 dark:text-white">OAuth Only</h4>
                                                <span class="ml-auto text-[10px] font-medium px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10">Paling Sederhana</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">SSO hanya mengelola autentikasi. User login via SSO, lalu aplikasi client membuat akun lokal secara otomatis dari data OAuth.</p>
                                            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 space-y-2.5">
                                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Langkah Setup di Client App:</p>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold flex items-center justify-center">1</span>
                                                    <p class="text-xs">Daftarkan aplikasi di SSO Server (form ini) &mdash; dapatkan <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">Client ID</code> dan <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">Client Secret</code></p>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold flex items-center justify-center">2</span>
                                                    <div class="text-xs">
                                                        <p>Tambahkan ke file <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">.env</code> client app:</p>
                                                        <pre class="mt-1.5 p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-[11px] font-mono overflow-x-auto">SSO_CLIENT_ID="your-client-id"
SSO_CLIENT_SECRET="your-client-secret"
SSO_BASE_URL="http://sso-server-url"
SSO_REDIRECT_URI="http://your-app/auth/callback"</pre>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold flex items-center justify-center">3</span>
                                                    <p class="text-xs">Implementasikan OAuth callback di client app (route <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">/auth/callback</code>) untuk menerima authorization code dan menukarnya dengan access token.</p>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold flex items-center justify-center">4</span>
                                                    <p class="text-xs">Gunakan access token untuk memanggil <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">GET /api/user</code> di SSO Server untuk mendapatkan data user.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-200 dark:border-gray-700"></div>

                                        {{-- API Sync --}}
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-6 h-6 rounded-lg bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.556a4.5 4.5 0 00-1.242-7.244l4.5-4.5a4.5 4.5 0 016.364 6.364l-1.757 1.757"/></svg>
                                                </span>
                                                <h4 class="font-semibold text-gray-800 dark:text-white">API Sync</h4>
                                                <span class="ml-auto text-[10px] font-medium px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 ring-1 ring-cyan-600/10">Direkomendasikan</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">SSO Server mengirim data user secara real-time melalui REST API ke endpoint yang disediakan aplikasi client. Aman, terenkripsi, dan mudah dikelola.</p>
                                            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 space-y-2.5">
                                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Langkah Setup di Client App:</p>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400 text-[10px] font-bold flex items-center justify-center">1</span>
                                                    <p class="text-xs">Lakukan semua langkah <strong>OAuth Only</strong> di atas terlebih dahulu.</p>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400 text-[10px] font-bold flex items-center justify-center">2</span>
                                                    <div class="text-xs">
                                                        <p>Buat API endpoints berikut di client app:</p>
                                                        <pre class="mt-1.5 p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-[11px] font-mono overflow-x-auto">GET  /api/sso/ping          # Health check
POST /api/sso/users/sync     # Create/update user
POST /api/sso/users/remove   # Deactivate user
GET  /api/sso/users          # List all users</pre>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400 text-[10px] font-bold flex items-center justify-center">3</span>
                                                    <div class="text-xs">
                                                        <p>Buat middleware verifikasi secret di client app:</p>
                                                        <pre class="mt-1.5 p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-[11px] font-mono overflow-x-auto">// Verifikasi header X-SSO-Secret
$secret = $request->header('X-SSO-Secret');
if (!hash_equals($expectedSecret, $secret)) {
    return response()->json(['error' => 'Unauthorized'], 401);
}</pre>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400 text-[10px] font-bold flex items-center justify-center">4</span>
                                                    <div class="text-xs">
                                                        <p>Tambahkan ke <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">.env</code> client app:</p>
                                                        <pre class="mt-1.5 p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-[11px] font-mono overflow-x-auto">SSO_API_SECRET="same-secret-as-sso-server"</pre>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-400 text-[10px] font-bold flex items-center justify-center">5</span>
                                                    <p class="text-xs">Isi <strong>API Base URL</strong> dan <strong>API Secret Key</strong> di form ini, lalu klik <strong>Test Koneksi API</strong> untuk memverifikasi.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-200 dark:border-gray-700"></div>

                                        {{-- Direct DB --}}
                                        <div>
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="w-6 h-6 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375"/></svg>
                                                </span>
                                                <h4 class="font-semibold text-gray-800 dark:text-white">Direct Database</h4>
                                                <span class="ml-auto text-[10px] font-medium px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-600/10">Legacy / Internal</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">SSO Server langsung mengakses database client app untuk sinkronisasi data user. Gunakan jika client app tidak menyediakan API endpoint.</p>
                                            <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 space-y-2.5">
                                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 mb-2">Langkah Setup:</p>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-400 text-[10px] font-bold flex items-center justify-center">1</span>
                                                    <p class="text-xs">Lakukan semua langkah <strong>OAuth Only</strong> terlebih dahulu.</p>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-400 text-[10px] font-bold flex items-center justify-center">2</span>
                                                    <p class="text-xs">Pastikan database client app dapat diakses dari server SSO (port terbuka, firewall dikonfigurasi).</p>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-400 text-[10px] font-bold flex items-center justify-center">3</span>
                                                    <div class="text-xs">
                                                        <p>Pastikan tabel <code class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded text-[11px]">users</code> di database client memiliki kolom:</p>
                                                        <pre class="mt-1.5 p-2 bg-gray-200 dark:bg-gray-700 rounded-lg text-[11px] font-mono overflow-x-auto">id, name, email, is_active, updated_at</pre>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-400 text-[10px] font-bold flex items-center justify-center">4</span>
                                                    <p class="text-xs">Isi credential database (driver, host, port, database, username, password) di form ini.</p>
                                                </div>
                                                <div class="flex gap-2.5">
                                                    <span class="shrink-0 w-5 h-5 rounded-full bg-violet-100 dark:bg-violet-900/50 text-violet-700 dark:text-violet-400 text-[10px] font-bold flex items-center justify-center">5</span>
                                                    <p class="text-xs">Klik <strong>Test Koneksi Database</strong> untuk memverifikasi koneksi berhasil.</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                                                <p class="text-[11px] text-amber-700 dark:text-amber-400 flex items-start gap-1.5">
                                                    <svg class="w-3.5 h-3.5 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                                    <strong>Perhatian:</strong> Metode ini menyimpan credential database client di SSO Server. Pastikan hanya digunakan di jaringan internal atau via koneksi terenkripsi (SSL/TLS).
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Guide Footer --}}
                                    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700 shrink-0">
                                        <button type="button" @click="showGuide = false" class="w-full px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition">
                                            Tutup Panduan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Database Config --}}
                        @if($sync_method === 'database')
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Konfigurasi Database</h4>
                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-600/10">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                    Terenkripsi
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Driver <span class="text-red-500">*</span></label>
                                    <x-searchable-select
                                        wire:model="db_driver"
                                        :options="[
                                            ['value' => 'pgsql', 'label' => 'PostgreSQL'],
                                            ['value' => 'mysql', 'label' => 'MySQL / MariaDB'],
                                            ['value' => 'sqlsrv', 'label' => 'SQL Server'],
                                            ['value' => 'sqlite', 'label' => 'SQLite'],
                                        ]"
                                        placeholder="Pilih driver..."
                                        searchPlaceholder="Cari driver..."
                                    />
                                    @error('db_driver') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Pilih jenis database yang digunakan client app.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Port <span class="text-red-500">*</span></label>
                                    <input wire:model="db_port" type="text" class="w-full px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="5432">
                                    @error('db_port') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Contoh: <code class="text-emerald-600 dark:text-emerald-400">5432</code> (PostgreSQL), <code class="text-emerald-600 dark:text-emerald-400">3306</code> (MySQL), <code class="text-emerald-600 dark:text-emerald-400">1433</code> (SQL Server).</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Host <span class="text-red-500">*</span></label>
                                <input wire:model="db_host" type="text" class="w-full px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="127.0.0.1">
                                @error('db_host') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">contoh: <code class="text-emerald-600 dark:text-emerald-400">127.0.0.1</code> atau <code class="text-emerald-600 dark:text-emerald-400">db.company.com</code></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Database <span class="text-red-500">*</span></label>
                                <input wire:model="db_database" type="text" class="w-full px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="db_helpdesk">
                                @error('db_database') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Nama database di server client app.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Username <span class="text-red-500">*</span></label>
                                    <input wire:model="db_username" type="text" class="w-full px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="postgres">
                                    @error('db_username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">User database dengan akses baca/tulis.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Password</label>
                                    <div class="relative">
                                        <input wire:model="db_password" :type="showDbPass ? 'text' : 'password'" class="w-full px-3 py-2.5 pr-9 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="********">
                                        <button type="button" @click="showDbPass = !showDbPass" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                            <svg x-show="!showDbPass" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <svg x-show="showDbPass" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                        </button>
                                    </div>
                                    @error('db_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Password database, dikosongkan jika tidak diperlukan.</p>
                                </div>
                            </div>

                            <button type="button" wire:click="testDatabaseConnection" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition" wire:loading.attr="disabled" wire:target="testDatabaseConnection">
                                <span wire:loading.class="hidden" wire:target="testDatabaseConnection">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                </span>
                                <span wire:loading wire:target="testDatabaseConnection">
                                    <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </span>
                                Test Koneksi Database
                            </button>
                        </div>
                        @endif

                        {{-- API Config --}}
                        @if($sync_method === 'api')
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 space-y-4">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.556a4.5 4.5 0 00-1.242-7.244l4.5-4.5a4.5 4.5 0 016.364 6.364l-1.757 1.757"/></svg>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Konfigurasi API</h4>
                                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-600/10">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                    Terenkripsi
                                </span>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">API Base URL <span class="text-red-500">*</span></label>
                                <input wire:model="api_base_url" type="url" class="w-full px-3 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="https://helpdesk.company.com/api">
                                @error('api_base_url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">contoh: <code class="text-emerald-600 dark:text-emerald-400">https://helpdesk.company.com/api</code></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">API Secret Key <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input wire:model="api_secret_key" :type="showApiKey ? 'text' : 'password'" class="w-full px-3 py-2.5 pr-9 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm font-mono" placeholder="sk-xxxxxxxxxxxxxxxx">
                                    <button type="button" @click="showApiKey = !showApiKey" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                        <svg x-show="!showApiKey" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <svg x-show="showApiKey" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Secret key yang sama harus dikonfigurasi di sisi client app.</p>
                                @error('api_secret_key') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <button type="button" wire:click="testApiConnection" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition" wire:loading.attr="disabled" wire:target="testApiConnection">
                                <span wire:loading.class="hidden" wire:target="testApiConnection">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                </span>
                                <span wire:loading wire:target="testApiConnection">
                                    <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </span>
                                Test Koneksi API
                            </button>
                        </div>
                        @endif

                        {{-- Test Connection Result --}}
                        @if($testConnectionResult)
                        <div class="p-3 rounded-lg text-sm {{ $testConnectionStatus === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                            <div class="flex items-center gap-2">
                                @if($testConnectionStatus === 'success')
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                @endif
                                <span class="text-xs break-all">{{ $testConnectionResult }}</span>
                            </div>
                        </div>
                        @endif

                        @if($sync_method === 'none')
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-dashed border-gray-300 dark:border-gray-600 text-center">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375"/></svg>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Pilih metode sinkronisasi untuk mengkonfigurasi koneksi remote ke aplikasi client.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <x-cancel-button wire:click="closeModal" target="closeModal" />
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            {{ $isEditing ? 'Simpan Perubahan' : 'Daftarkan Aplikasi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
