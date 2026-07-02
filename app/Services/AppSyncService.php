<?php

namespace App\Services;

use App\Models\ClientApp;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppSyncService
{
    private function buildApiClient(ClientApp $app): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(10)
            ->withHeaders([
                'X-SSO-Secret' => $app->api_secret_key,
                'Accept' => 'application/json',
            ])
            ->baseUrl(rtrim($app->api_base_url, '/'));
    }

    private function getApiAppsForUser(User $user): \Illuminate\Support\Collection
    {
        if ($user->hasRole('super-admin')) {
            return $this->getAllApiApps();
        }

        return $user->clientApps()
            ->where('is_active', true)
            ->where('sync_method', 'api')
            ->whereNotNull('api_base_url')
            ->whereNotNull('api_secret_key')
            ->get();
    }

    private function getAllApiApps(): \Illuminate\Support\Collection
    {
        return ClientApp::where('is_active', true)
            ->where('sync_method', 'api')
            ->whereNotNull('api_base_url')
            ->whereNotNull('api_secret_key')
            ->get();
    }

    public function syncUserToApps(User $user): void
    {
        foreach ($this->getApiAppsForUser($user) as $app) {
            $this->syncUserToApp($app, $user);
        }
    }

    /**
     * Sync user profile data only (name, email, is_active).
     * SSO Server roles are NOT sent — client app roles are independent
     * and managed via RemoteAppManager.
     */
    public function syncUserToApp(ClientApp $app, User $user): array
    {
        if (!$app->hasApiConfig()) {
            return ['success' => false, 'message' => 'No API config.'];
        }
        try {
            $resp = $this->buildApiClient($app)->post('/sso/users/sync', [
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
            ]);
            Log::info('AppSync: user synced', ['user' => $user->email, 'app' => $app->name, 'ok' => $resp->successful()]);
            return ['success' => $resp->successful(), 'message' => $resp->successful() ? 'Synced.' : 'HTTP ' . $resp->status()];
        } catch (\Exception $e) {
            Log::error('AppSync: user sync error', ['user' => $user->email, 'app' => $app->name, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function removeUserFromApps(User $user): void
    {
        foreach ($this->getApiAppsForUser($user) as $app) {
            $this->removeUserFromApp($app, $user);
        }
    }

    public function removeUserFromApp(ClientApp $app, User $user): array
    {
        if (!$app->hasApiConfig()) {
            return ['success' => false, 'message' => 'No API config.'];
        }
        try {
            $resp = $this->buildApiClient($app)->post('/sso/users/remove', ['email' => $user->email]);
            return ['success' => $resp->successful(), 'message' => $resp->successful() ? 'Removed.' : 'HTTP ' . $resp->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // NOTE: SSO Server roles and Client App roles are independent systems.
    // Role/permission management on client apps is done exclusively via RemoteAppManager.
    // AppSyncService only handles user profile data propagation (name, email, is_active).

    public function onAccessGranted(ClientApp $app, User $user): void
    {
        $this->syncUserToApp($app, $user);
    }

    public function onAccessRevoked(ClientApp $app, User $user): void
    {
        $this->removeUserFromApp($app, $user);
    }
}
