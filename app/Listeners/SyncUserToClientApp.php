<?php

namespace App\Listeners;

use App\Models\ClientApp;
use App\Models\User;
use App\Services\RemoteAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Events\AccessTokenCreated;

class SyncUserToClientApp implements ShouldQueue
{
    public function __construct(
        private readonly RemoteAppService $remoteAppService,
    ) {}

    public function handle(AccessTokenCreated $event): void
    {
        if (! $event->userId) {
            return;
        }

        $user = User::find($event->userId);

        if (! $user) {
            return;
        }

        $clientApp = ClientApp::where('oauth_client_id', $event->clientId)
            ->where('is_active', true)
            ->first();

        if (! $clientApp) {
            return;
        }

        // Only sync if the client app has a sync method configured
        if ($clientApp->sync_method === ClientApp::SYNC_NONE) {
            Log::info('SSO sync skipped: client app has no sync method', [
                'user_id' => $user->id,
                'client_app' => $clientApp->name,
            ]);
            return;
        }

        $userData = [
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
        ];

        try {
            $result = $this->remoteAppService->syncUserToApp($clientApp, $userData);

            Log::info('SSO auto-sync result', [
                'user_id' => $user->id,
                'email' => $user->email,
                'client_app' => $clientApp->name,
                'result' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('SSO auto-sync failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'client_app' => $clientApp->name,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
