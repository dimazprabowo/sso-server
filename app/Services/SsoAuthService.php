<?php

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SsoAuthService
{
    public function attemptLogin(string $email, string $password, string $ipAddress, string $userAgent, bool $remember = false): ?User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        if (! $user->isActive()) {
            return null;
        }

        Auth::login($user, $remember);

        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_at' => now(),
        ]);

        return $user;
    }

    public function logout(User $user): void
    {
        $tokens = $user->tokens()->where('revoked', false)->get();

        foreach ($tokens as $token) {
            $token->refreshToken?->revoke();
            $token->revoke();
        }

        $user->loginHistories()
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        Auth::logout();
    }

    public function revokeTokensForClient(User $user, string $clientId): void
    {
        $tokens = $user->tokens()->where('client_id', $clientId)->where('revoked', false)->get();

        foreach ($tokens as $token) {
            $token->refreshToken?->revoke();
            $token->revoke();
        }
    }
}
