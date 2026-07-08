<div class="min-h-screen flex flex-col lg:flex-row relative"
    @if($this->isRecaptchaEnabled())
    x-data="{
        recaptchaRendered: false,
        recaptchaWidgetId: null,
        renderRecaptcha() {
            if (this.recaptchaRendered || !window.grecaptcha) return;
            const container = document.getElementById('recaptcha-container');
            if (!container) return;
            try {
                this.recaptchaWidgetId = grecaptcha.render(container, {
                    sitekey: '{{ $this->getRecaptchaSiteKey() }}',
                    callback: (token) => { $wire.set('recaptchaToken', token); },
                    'expired-callback': () => { $wire.set('recaptchaToken', null); },
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                });
                this.recaptchaRendered = true;
            } catch(e) {}
        },
        reloadRecaptcha() {
            $wire.set('recaptchaToken', null);
            if (this.recaptchaRendered && this.recaptchaWidgetId !== null && window.grecaptcha) {
                try {
                    grecaptcha.reset(this.recaptchaWidgetId);
                } catch(e) {
                    this.recaptchaRendered = false;
                    this.recaptchaWidgetId = null;
                    this.renderRecaptcha();
                }
            } else {
                this.recaptchaRendered = false;
                this.recaptchaWidgetId = null;
                this.renderRecaptcha();
            }
        },
        init() {
            const interval = setInterval(() => {
                if (window.grecaptcha && grecaptcha.render) {
                    this.renderRecaptcha();
                    clearInterval(interval);
                }
            }, 200);
        }
    }"
    x-on:reset-recaptcha.window="reloadRecaptcha()"
    @endif
>
    {{-- Dark Mode Toggle - Fixed Position --}}
    <div class="fixed top-4 right-4 z-50">
        <button @click="$store.darkMode.toggle()"
                class="p-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700">
            <svg x-show="!$store.darkMode.dark" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            <svg x-show="$store.darkMode.dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
    </div>

    {{-- Left Panel — Branding --}}
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
                    Satu Akun,<br>Semua Akses.
                </h2>
                <p class="text-lg lg:text-xl text-emerald-100 leading-relaxed">
                    Single Sign-On terpusat untuk mengelola autentikasi seluruh aplikasi secara aman dan efisien.
                </p>
                <div class="space-y-4 pt-8">
                    <div class="flex items-start space-x-4 text-emerald-50">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-1">Keamanan Terpusat</h3>
                            <p class="text-sm text-emerald-200">Autentikasi aman dengan OAuth 2.0 dan enkripsi end-to-end</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 text-emerald-50">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-1">Role-Based Access Control</h3>
                            <p class="text-sm text-emerald-200">Manajemen role dan permission yang fleksibel</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4 text-emerald-50">
                        <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white mb-1">Multi-App Management</h3>
                            <p class="text-sm text-emerald-200">Kelola akses user ke semua aplikasi dari satu dashboard</p>
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
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Masuk ke akun Anda</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm sm:text-base">Gunakan akun SSO untuk mengakses semua aplikasi</p>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Form --}}
                <form wire:submit="login" class="space-y-6">
                    {{-- Email --}}
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

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                            <a href="{{ route('password.request') }}" wire:navigate class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 font-medium transition">
                                Lupa password?
                            </a>
                        </div>
                        <div class="relative">
                            <input
                                wire:model="password"
                                :type="$wire.showPassword ? 'text' : 'password'"
                                id="password"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                class="w-full px-4 py-2.5 pr-11 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition text-sm"
                            >
                            <button type="button" wire:click="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                                <svg x-show="!$wire.showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg x-show="$wire.showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500 dark:text-red-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me + Forgot Password --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input wire:model="remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500/40 transition bg-white dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                        </label>
                    </div>

                    @if($this->isRecaptchaEnabled())
                    {{-- reCAPTCHA --}}
                    <div class="space-y-3">
                        <div id="recaptcha-wrapper" class="flex justify-center">
                            <div id="recaptcha-container" wire:ignore></div>
                        </div>
                        <div class="flex items-center justify-center">
                            <button type="button" @click="reloadRecaptcha()" class="inline-flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Reload reCAPTCHA</span>
                            </button>
                        </div>
                        @error('recaptcha')
                            <p class="text-sm text-red-500 dark:text-red-400 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 gap-2 text-sm disabled:opacity-70 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="login"
                    >
                        <span class="inline-flex items-center justify-center gap-2">
                            <!-- ICON LOADING -->
                            <svg wire:loading wire:target="login"
                                class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>

                            <!-- TEKS NORMAL -->
                            <span wire:loading.class="hidden" wire:target="login">
                                Masuk
                            </span>

                            <!-- TEKS LOADING -->
                            <span wire:loading wire:target="login">
                                Memproses...
                            </span>
                        </span>
                    </button>

                    @if($this->isRecaptchaEnabled())
                    {{-- reCAPTCHA Info --}}
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        This site is protected by reCAPTCHA and the Google
                        <a href="https://policies.google.com/privacy" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline">Privacy Policy</a> and
                        <a href="https://policies.google.com/terms" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline">Terms of Service</a> apply.
                    </div>
                    @endif
                </form>
            </div>

            {{-- Footer --}}
            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                Butuh bantuan? <a href="#" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">Hubungi Support</a>
            </p>
        </div>
    </div>
</div>
