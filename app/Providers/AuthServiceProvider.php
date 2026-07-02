<?php

namespace App\Providers;

use App\Models\ClientApp;
use App\Models\PassportClient;
use App\Models\User;
use App\Policies\ClientAppPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        ClientApp::class => ClientAppPolicy::class,
        Role::class => RolePolicy::class,
    ];

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        Passport::useClientModel(PassportClient::class);
        Passport::tokensExpireIn(now()->addMinutes((int) config('passport.token_expiration', 60)));
        Passport::refreshTokensExpireIn(now()->addMinutes((int) config('passport.refresh_token_expiration', 43200)));
        Passport::personalAccessTokensExpireIn(now()->addMinutes((int) config('passport.personal_access_token_expiration', 360)));

        Passport::authorizationView('passport::authorize');
        Passport::deviceAuthorizationView('passport::device.authorize');

        Passport::tokensCan([
            'read-user' => 'Read user profile information',
            'manage-user' => 'Manage user profile',
        ]);

        Passport::setDefaultScope([
            'read-user',
        ]);
    }
}
