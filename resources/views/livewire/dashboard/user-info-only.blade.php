{{-- Partial: User Info Only View (for users without dashboard_view permission) --}}
<div class="space-y-6">
    {{-- Welcome Hero --}}
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-700 dark:from-emerald-800 dark:via-emerald-900 dark:to-teal-900 rounded-2xl p-8 text-white relative overflow-hidden shadow-lg">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full translate-x-1/3 -translate-y-1/3"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                {{-- Avatar --}}
                <div class="flex-shrink-0 w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-3xl font-bold uppercase ring-4 ring-white/30 shadow-lg">
                    {{ mb_substr($user->name, 0, 1) }}
                </div>
                {{-- Greeting --}}
                <div class="text-center md:text-left flex-1">
                    <p class="text-emerald-100 text-sm font-medium mb-1">Selamat datang kembali,</p>
                    <h1 class="text-3xl font-bold mb-2">{{ $user->name }}</h1>
                    <p class="text-emerald-200 text-sm mb-3">Portal Single Sign-On PT BKI</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2">
                        <span class="inline-flex items-center whitespace-nowrap gap-1.5 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1.5 text-xs font-semibold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            {{ $user->getRoleNames()->join(', ') ?: 'User' }}
                        </span>
                        @if($user->is_active)
                        <span class="inline-flex items-center whitespace-nowrap gap-1.5 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1.5 text-xs font-semibold">
                            <span class="w-1.5 h-1.5 bg-emerald-300 rounded-full animate-pulse"></span>
                            Akun Aktif
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Information Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">Informasi Akun</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Detail informasi akun Anda</p>
            </div>
            <a href="{{ route('profile') }}" wire:navigate
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                </svg>
                Edit Profil
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Name --}}
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Nama Lengkap</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $user->name }}</p>
                </div>
            </div>

            {{-- Email --}}
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Email</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $user->email }}</p>
                </div>
            </div>

            {{-- Role --}}
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Role</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $user->getRoleNames()->join(', ') ?: 'User' }}</p>
                </div>
            </div>

            {{-- Join Date --}}
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Bergabung</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-lg {{ $user->is_active ? 'bg-emerald-50 dark:bg-emerald-900/30' : 'bg-red-50 dark:bg-red-900/30' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $user->is_active ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        @if($user->is_active)
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        @endif
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Status Akun</p>
                    <span class="inline-flex items-center whitespace-nowrap gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/10' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 ring-1 ring-red-600/10' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            {{-- Active Tokens --}}
            @php $tokenCount = $user->tokens()->where('revoked', false)->count(); @endphp
            <div class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Token Aktif</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $tokenCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Notice --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-1">Informasi Akses</h3>
                <p class="text-sm text-blue-700 dark:text-blue-400">
                    Untuk mengakses aplikasi yang tersedia, hubungi administrator sistem untuk mendapatkan izin akses yang sesuai dengan kebutuhan Anda.
                </p>
            </div>
        </div>
    </div>
</div>
