<?php

namespace Database\Seeders;

use App\Models\ClientApp;
use App\Models\User;
use App\Services\ClientAppService;
use Illuminate\Database\Seeder;

class ClientAppSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(ClientAppService::class);

        $apiSecret = env('CLIENT_API_SECRET', 'client-sso-secret-key-2026');

        // Client App (Boilerplate)
        $existing = ClientApp::where('name', 'Boilerplate')->first();

        if (! $existing) {
            $result = $service->create([
                'name' => 'Boilerplate',
                'domain' => 'http://localhost:8999',
                'redirect_uri' => 'http://localhost:8999/auth/callback',
                'description' => 'Default boilerplate client application',
                'is_active' => true,
                'sync_method' => 'api',
                'api_base_url' => 'http://localhost:8999/api',
                'api_secret_key' => $apiSecret,
            ]);

            $clientId = $result['client_id'];
            $clientSecret = $result['client_secret'];

            $this->command->newLine();
            $this->command->info('╔══════════════════════════════════════════════════════════════════╗');
            $this->command->info('║             CLIENT APP CREATED SUCCESSFULLY                     ║');
            $this->command->info('╠══════════════════════════════════════════════════════════════════╣');
            $this->command->info("║  App Name      : Boilerplate");
            $this->command->info("║  Client ID     : {$clientId}");
            $this->command->info("║  Client Secret : {$clientSecret}");
            $this->command->info("║  API Secret    : {$apiSecret}");
            $this->command->info('╠══════════════════════════════════════════════════════════════════╣');
            $this->command->warn('║  Update client app .env with:');
            $this->command->warn("║  SSO_CLIENT_ID=\"{$clientId}\"");
            $this->command->warn("║  SSO_CLIENT_SECRET=\"{$clientSecret}\"");
            $this->command->warn("║  SSO_API_SECRET=\"{$apiSecret}\"");
            $this->command->info('╚══════════════════════════════════════════════════════════════════╝');
            $this->command->newLine();
            // Grant test user access to this app
            $testUser = User::where('email', 'user@company.com')->first();
            if ($testUser) {
                $result['app']->users()->syncWithoutDetaching([
                    $testUser->id => ['granted_at' => now(), 'granted_by' => 1],
                ]);
                $this->command->info("  Granted app access to user@company.com");
            }
        } else {
            $existing->update([
                'sync_method' => 'api',
                'api_base_url' => 'http://localhost:8999/api',
                'api_secret_key' => $apiSecret,
            ]);
            $this->command->info("Client App '{$existing->name}' already exists (id={$existing->id}), sync config updated.");
            $this->command->warn("OAuth credentials remain unchanged. Use 'Regenerate Secret' in UI if needed.");
        }
    }
}
