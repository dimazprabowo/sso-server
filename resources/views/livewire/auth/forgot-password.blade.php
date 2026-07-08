<div class="min-h-screen flex flex-col lg:flex-row relative">
    {{-- Dark Mode Toggle - Fixed Position (konsisten dengan login) --}}
    <div class="fixed top-4 right-4 z-50">
        <button @click="$store.darkMode.toggle()"
                class="p-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700">
            <svg x-show="!$store.darkMode.dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="$store.darkMode.dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
    </div>

    {{-- Left Panel — Branding (konsisten dengan login) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 dark:from-emerald-800 dark:via-emerald-900 dark:to-gray-900 p-8 lg:p-12 flex-col justify-between relative overflow-hidden">
        {{-- Decorative Background Elements --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>

        <div class="relative z-10">
            {{-- Logo --}}
            <div class="flex items-center space-x-3 mb-12">
                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
                    <img src="{{ email_logo_url() }}" alt="BKI Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                <div class="text-white">
                    <h1 class="text-2xl lg:text-3xl font-bold">{{ config('app.name') }}</h1>
                    <p class="text-sm text-emerald-100">Single Sign-On Server</p>
                </div>
            </div>

            {{-- Center Content --}}
            <div class="space-y-6 max-w-lg">
                <h2 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white leading-tight">
                    Lupa Password?
                </h2>
                <p class="text-lg lg:text-xl text-emerald-100 leading-relaxed">
                    Jangan khawatir, kami akan mengirimkan instruksi untuk mereset password Anda.
                </p>
                <div class="space-y-4 pt-8">
                    <div class="flex items-start space-x-4 text-emerald-50">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-1">Link Reset via Email</h3>
                            <p class="text-sm text-emerald-200">Kami akan mengirimkan link reset password ke email terdaftar Anda</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 text-emerald-50">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-1">Link Kedaluwarsa 60 Menit</h3>
                            <p class="text-sm text-emerald-200">Untuk keamanan, link reset password hanya berlaku selama 60 menit</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 text-emerald-50">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-1">Keamanan Terjamin</h3>
                            <p class="text-sm text-emerald-200">Token terenkripsi dan hanya bisa digunakan sekali</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 text-emerald-100 text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>

    {{-- Right Panel — Form --}}
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen lg:min-h-0">
        <div class="w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="lg:hidden flex flex-col items-center mb-8">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
                        <img src="{{ email_logo_url() }}" alt="BKI Logo" class="w-full h-full object-contain rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Single Sign-On Server</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
                {{-- Header --}}
                <div class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Lupa Password</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm sm:text-base">Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.</p>
                </div>

                @if($emailSent)
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-medium text-green-800 dark:text-green-300">Email terkirim!</p>
                                <p class="text-sm text-green-600 dark:text-green-400 mt-1">Link reset password telah dikirim ke <strong>{{ $email }}</strong>. Periksa inbox atau folder spam Anda.</p>
                            </div>
                        </div>
                    </div>

                    <button type="button" wire:click="$set('emailSent', false)"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-xl font-semibold text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none transition-all duration-200">
                        Kirim Ulang
                    </button>
                @else
                    {{-- Form --}}
                    <form wire:submit="sendResetLink" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input
                                wire:model="email"
                                type="email"
                                id="email"
                                placeholder="nama@company.com"
                                autocomplete="email"
                                autofocus
                                class="w-full px-4 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm"
                            >
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 gap-2 text-sm"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed"
                        >
                            <svg wire:loading wire:target="sendResetLink" class="animate-spin h-5 w-5 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span wire:loading.class="hidden" wire:target="sendResetLink">Kirim Link Reset</span>
                            <span wire:loading wire:target="sendResetLink">Mengirim...</span>
                        </button>
                    </form>
                @endif

                {{-- Back to login --}}
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" wire:navigate class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 font-medium transition inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        Kembali ke halaman login
                    </a>
                </div>
            </div>

            {{-- Mobile Footer --}}
            <p class="lg:hidden text-center text-gray-400 dark:text-gray-500 text-xs mt-8">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</div>
