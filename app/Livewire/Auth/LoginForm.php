<?php

namespace App\Livewire\Auth;

use App\Livewire\Traits\HasNotification;
use App\Services\SsoAuthService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
class LoginForm extends Component
{
    use HasNotification;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;
    public bool $showPassword = false;
    public ?string $recaptchaToken = null;

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function login(SsoAuthService $authService): void
    {
        try {
            // Step 1: Verify reCAPTCHA FIRST (before field validation) - if enabled
            if (config('services.recaptcha.enabled')) {
                $this->verifyRecaptcha();
            }

            // Step 2: Validate form fields
            $this->validate();

            // Step 3: Check rate limiting
            $this->ensureIsNotRateLimited();

            // Step 4: Attempt authentication
            $user = $authService->attemptLogin(
                email: $this->email,
                password: $this->password,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                remember: $this->remember,
            );

            if (! $user) {
                $throttleKey = $this->throttleKey();
                RateLimiter::hit($throttleKey, 60);
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'Email atau password salah.',
                ]);
            }

            // Step 5: Check if user is active
            if (! $user->isActive()) {
                auth()->logout();
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ]);
            }

            // Success - clear rate limiter
            RateLimiter::clear($this->throttleKey());
            session()->regenerate();

            $intended = session()->pull('url.intended', route('dashboard'));
            $isOAuth = str_contains($intended, '/oauth/');

            if ($isOAuth) {
                $this->notifySuccess('Login berhasil! Mengalihkan ke aplikasi...');
            } else {
                $this->notifySuccess('Login berhasil! Selamat datang kembali.');
            }

            $this->redirect($intended, navigate: ! $isOAuth);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get first error message for notification
            $errors = $e->validator->errors();
            $firstError = $errors->first();
            $this->notifyError($firstError);
            
            // Re-throw to show field errors
            throw $e;
            
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
            
            \Log::error('SSO Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Verify reCAPTCHA v2 token.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function verifyRecaptcha(): void
    {
        // Check if token exists
        if (empty($this->recaptchaToken)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'recaptcha' => 'Silakan centang reCAPTCHA untuk melanjutkan.',
            ]);
        }

        // Verify with Google
        try {
            $response = Http::timeout(10)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $this->recaptchaToken,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->successful()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'recaptcha' => 'Gagal menghubungi server reCAPTCHA. Silakan coba lagi.',
                ]);
            }

            $result = $response->json();

            if (!isset($result['success']) || !$result['success']) {
                \Log::error('reCAPTCHA v2 verification failed', [
                    'error_codes' => $result['error-codes'] ?? [],
                ]);
                
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'recaptcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                ]);
            }

            \Log::info('reCAPTCHA v2 verification successful', [
                'ip' => request()->ip(),
            ]);
            
        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('reCAPTCHA request failed', [
                'error' => $e->getMessage()
            ]);
            
            throw \Illuminate\Validation\ValidationException::withMessages([
                'recaptcha' => 'Gagal verifikasi reCAPTCHA. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return 'login:' . str($this->email)->lower() . '|' . request()->ip();
    }

    /**
     * Check if reCAPTCHA is enabled
     */
    public function isRecaptchaEnabled(): bool
    {
        return config('services.recaptcha.enabled', false);
    }

    /**
     * Get reCAPTCHA site key
     */
    public function getRecaptchaSiteKey(): string
    {
        return config('services.recaptcha.site_key', '');
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
