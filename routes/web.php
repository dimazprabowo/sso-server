<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\ClientAppManager;
use App\Livewire\Dashboard;
use App\Livewire\RemoteAppManager;
use App\Livewire\Pages\Profile;
use App\Livewire\RolePermissionManager;
use App\Livewire\UserAppAccess;
use App\Livewire\UserManager;
use App\Models\ClientApp;
use App\Services\SsoAuthService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Logout
Route::post('/logout', function (SsoAuthService $authService) {
    if (auth()->check()) {
        $authService->logout(auth()->user());
    }

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $redirectUrl = request()->input('redirect_url');

    if ($redirectUrl) {
        $allowedDomains = ClientApp::where('is_active', true)
            ->pluck('domain')
            ->merge(ClientApp::whereNotNull('post_logout_redirect_uri')->pluck('post_logout_redirect_uri'))
            ->filter()
            ->toArray();

        $parsed = parse_url($redirectUrl, PHP_URL_HOST);
        $isAllowed = false;

        foreach ($allowedDomains as $allowed) {
            $allowedHost = parse_url($allowed, PHP_URL_HOST) ?: $allowed;
            if ($parsed === $allowedHost) {
                $isAllowed = true;
                break;
            }
        }

        if (! $isAllowed) {
            $redirectUrl = route('login');
        }
    } else {
        $redirectUrl = route('login');
    }

    return redirect($redirectUrl);
})->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/users', UserManager::class)->middleware('can:users_view')->name('users.index');
    Route::get('/roles', RolePermissionManager::class)->middleware('can:roles_view')->name('roles.index');
    Route::get('/client-apps', ClientAppManager::class)->middleware('can:client_apps_view')->name('client-apps.index');
    Route::get('/client-apps/{app}/manage', RemoteAppManager::class)->middleware('can:client_apps_manage')->name('client-apps.manage');
    Route::get('/user-access', UserAppAccess::class)->middleware('can:user_access_view')->name('user-access.index');
});
