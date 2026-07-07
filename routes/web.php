<?php

use App\Models\ClientApp;
use App\Services\SsoAuthService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');
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
    Route::view('/dashboard', 'pages.dashboard')->name('dashboard');
    Route::view('/profile', 'pages.profile')->name('profile');
    Route::view('/users', 'pages.users')->middleware('can:users_view')->name('users.index');
    Route::view('/roles', 'pages.roles')->middleware('can:roles_view')->name('roles.index');
    Route::view('/client-apps', 'pages.client-apps')->middleware('can:client_apps_view')->name('client-apps.index');
    Route::get('/client-apps/{app}/manage', function (ClientApp $app) {
        return view('pages.remote-app', ['app' => $app]);
    })->middleware('can:client_apps_manage')->name('client-apps.manage');
    Route::view('/user-access', 'pages.user-access')->middleware('can:user_access_view')->name('user-access.index');
});
