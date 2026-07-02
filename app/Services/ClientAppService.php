<?php

namespace App\Services;

use App\Models\ClientApp;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;

class ClientAppService
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
    ) {}

    /**
     * Create a new client app with its OAuth client.
     *
     * @return array{app: ClientApp, client_id: string, client_secret: string|null}
     */
    public function create(array $data): array
    {
        $oauthClient = $this->clientRepository->createAuthorizationCodeGrantClient(
            name: $data['name'],
            redirectUris: [$data['redirect_uri']],
            confidential: true,
        );

        $plainSecret = $oauthClient->plainSecret;

        $app = ClientApp::create([
            'oauth_client_id' => $oauthClient->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'domain' => $data['domain'],
            'redirect_uri' => $data['redirect_uri'],
            'post_logout_redirect_uri' => $data['post_logout_redirect_uri'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            ...$this->extractRemoteConfig($data),
        ]);

        return [
            'app' => $app,
            'client_id' => $oauthClient->id,
            'client_secret' => $plainSecret,
        ];
    }

    public function update(ClientApp $clientApp, array $data): ClientApp
    {
        $oauthClient = $clientApp->oauthClient;

        if ($oauthClient) {
            $oauthClient->name = $data['name'];
            $oauthClient->redirect_uris = [$data['redirect_uri']];
            $oauthClient->save();
        }

        $updateData = [
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'domain' => $data['domain'],
            'redirect_uri' => $data['redirect_uri'],
            'post_logout_redirect_uri' => $data['post_logout_redirect_uri'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            ...$this->extractRemoteConfig($data),
        ];

        $clientApp->update($updateData);

        return $clientApp->fresh();
    }

    private function extractRemoteConfig(array $data): array
    {
        $config = [
            'sync_method' => $data['sync_method'] ?? 'none',
        ];

        $syncMethod = $config['sync_method'];

        if ($syncMethod === 'database') {
            $config['db_driver'] = $data['db_driver'] ?? 'pgsql';
            $config['db_host'] = $data['db_host'] ?? null;
            $config['db_port'] = $data['db_port'] ?? null;
            $config['db_database'] = $data['db_database'] ?? null;
            $config['db_username'] = $data['db_username'] ?? null;
            $config['db_password'] = $data['db_password'] ?? null;
            $config['api_base_url'] = null;
            $config['api_secret_key'] = null;
        } elseif ($syncMethod === 'api') {
            $config['db_driver'] = null;
            $config['db_host'] = null;
            $config['db_port'] = null;
            $config['db_database'] = null;
            $config['db_username'] = null;
            $config['db_password'] = null;
            $config['api_base_url'] = $data['api_base_url'] ?? null;
            $config['api_secret_key'] = $data['api_secret_key'] ?? null;
        } else {
            $config['db_driver'] = null;
            $config['db_host'] = null;
            $config['db_port'] = null;
            $config['db_database'] = null;
            $config['db_username'] = null;
            $config['db_password'] = null;
            $config['api_base_url'] = null;
            $config['api_secret_key'] = null;
        }

        return $config;
    }

    public function delete(ClientApp $clientApp): void
    {
        $oauthClient = $clientApp->oauthClient;

        $clientApp->users()->detach();
        $clientApp->delete();

        if ($oauthClient) {
            $oauthClient->tokens()->each(function ($token) {
                $token->refreshToken?->revoke();
                $token->revoke();
            });
            $oauthClient->delete();
        }
    }

    public function regenerateSecret(ClientApp $clientApp): ?string
    {
        $oauthClient = $clientApp->oauthClient;

        if (! $oauthClient) {
            return null;
        }

        $this->clientRepository->regenerateSecret($oauthClient);

        return $oauthClient->plainSecret;
    }

    public function toggleActive(ClientApp $clientApp): void
    {
        $clientApp->is_active = ! $clientApp->is_active;
        $clientApp->save();

        if (! $clientApp->is_active && $clientApp->oauthClient) {
            $clientApp->oauthClient->update(['revoked' => true]);
        } elseif ($clientApp->is_active && $clientApp->oauthClient) {
            $clientApp->oauthClient->update(['revoked' => false]);
        }
    }
}
