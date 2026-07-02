<div x-data="{ launching: false, launchingApp: '' }">
    {{-- Full-screen Loading Overlay --}}
    <div x-show="launching" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 text-center max-w-sm mx-4">
            <svg class="animate-spin h-10 w-10 text-emerald-600 dark:text-emerald-400 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-4 text-sm font-semibold text-gray-800 dark:text-white" x-text="'Membuka ' + launchingApp + '...'"></p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mohon tunggu, Anda akan dialihkan ke aplikasi</p>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 dark:from-emerald-800 dark:via-emerald-900 dark:to-teal-900 rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 left-1/3 w-64 h-64 bg-white/5 rounded-full translate-y-1/2"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium mb-1">Selamat datang kembali,</p>
                        <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                        <p class="text-emerald-200 text-sm max-w-2xl">Kelola identitas dan akses aplikasi enterprise Anda dari satu platform terpusat yang aman dan terintegrasi.</p>
                    </div>
                    <div class="hidden lg:flex items-center gap-6">
                        @if(!empty($stats))
                            @isset($stats['totalUsers'])
                            <div class="text-center">
                                <p class="text-4xl font-bold text-white">{{ $stats['totalUsers'] }}</p>
                                <p class="text-emerald-200 text-xs mt-1">Total Users</p>
                            </div>
                            @endisset
                            @isset($stats['totalApps'])
                            <div class="text-center">
                                <p class="text-4xl font-bold text-white">{{ $stats['totalApps'] }}</p>
                                <p class="text-emerald-200 text-xs mt-1">Client Apps</p>
                            </div>
                            @endisset
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Applications Grid --}}
        <div>
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Aplikasi Terdaftar</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Akses aplikasi enterprise yang tersedia untuk Anda</p>
                </div>
                @if($apps->count() > 0)
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <span class="font-medium">{{ $apps->count() }} Aplikasi Tersedia</span>
                </div>
                @endif
            </div>

            @if($apps->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($apps as $app)
                    @php
                        $appUrl = $app->domain;
                        if ($appUrl && !preg_match('#^https?://#i', $appUrl)) {
                            $appUrl = 'https://' . $appUrl;
                        }
                        $hasActiveToken = in_array($app->oauth_client_id, $activeTokenClientIds);
                        $ssoLaunchUrl = $appUrl ? rtrim($appUrl, '/') . '/auth/sso/redirect' : null;
                        
                        $colors = [
                            'emerald' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'ring' => 'ring-emerald-500/10'],
                            'violet' => ['bg' => 'bg-violet-500', 'light' => 'bg-violet-50 dark:bg-violet-900/30', 'text' => 'text-violet-600 dark:text-violet-400', 'ring' => 'ring-violet-500/10'],
                            'blue' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-50 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400', 'ring' => 'ring-blue-500/10'],
                            'amber' => ['bg' => 'bg-amber-500', 'light' => 'bg-amber-50 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'ring' => 'ring-amber-500/10'],
                            'rose' => ['bg' => 'bg-rose-500', 'light' => 'bg-rose-50 dark:bg-rose-900/30', 'text' => 'text-rose-600 dark:text-rose-400', 'ring' => 'ring-rose-500/10'],
                            'indigo' => ['bg' => 'bg-indigo-500', 'light' => 'bg-indigo-50 dark:bg-indigo-900/30', 'text' => 'text-indigo-600 dark:text-indigo-400', 'ring' => 'ring-indigo-500/10'],
                        ];
                        $colorKeys = array_keys($colors);
                        $selectedColor = $colors[$colorKeys[$loop->index % count($colorKeys)]];
                    @endphp
                    
                    <div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-lg transition-all duration-200">
                        <div class="p-6">
                            {{-- App Icon & Status --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-14 h-14 rounded-xl {{ $selectedColor['bg'] }} flex items-center justify-center text-white text-xl font-bold shadow-sm">
                                    {{ strtoupper(substr($app->name, 0, 2)) }}
                                </div>
                                @if($hasActiveToken)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/10">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Sesi Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 ring-1 ring-gray-200 dark:ring-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Tersedia
                                    </span>
                                @endif
                            </div>

                            {{-- App Info --}}
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                                    {{ $app->name }}
                                </h3>
                                @if($app->description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-2">{{ $app->description }}</p>
                                @endif
                                @if($appUrl)
                                    <a href="{{ $appUrl }}" target="_blank" class="inline-flex items-center gap-1 text-xs {{ $selectedColor['text'] }} hover:underline">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>
                                        </svg>
                                        {{ parse_url($appUrl, PHP_URL_HOST) }}
                                    </a>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-2">
                                @if($ssoLaunchUrl)
                                    <a href="{{ $ssoLaunchUrl }}"
                                       x-data="{ btnLoading: false }"
                                       @click="btnLoading = true; launching = true; launchingApp = '{{ addslashes($app->name) }}'"
                                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-sm hover:shadow-md transition-all">
                                        <svg x-show="!btnLoading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                        </svg>
                                        <svg x-show="btnLoading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Buka Aplikasi
                                    </a>
                                @endif
                                @can('manage', $app)
                                    @if($app->hasDatabaseConfig() || $app->hasApiConfig())
                                        <a href="{{ route('client-apps.manage', $app) }}" 
                                           x-data="{ loading: false }"
                                           @click="loading = true"
                                           wire:navigate
                                           class="inline-flex items-center justify-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                           title="Kelola Remote App">
                                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <svg x-show="loading" x-cloak class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        </a>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            {{-- Empty State --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Belum Ada Aplikasi Terdaftar</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                    Anda belum memiliki akses ke aplikasi apapun. Hubungi administrator sistem untuk mendapatkan akses ke aplikasi enterprise.
                </p>
                @can('client_apps_view')
                    <a href="{{ route('client-apps.index') }}" wire:navigate
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Kelola Client Apps
                    </a>
                @endcan
            </div>
            @endif
        </div>
    </div>
</div>
