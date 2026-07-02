<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Client;

class PassportClient extends Client
{
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return true;
    }

    public function clientApp()
    {
        return $this->hasOne(ClientApp::class, 'oauth_client_id', 'id');
    }
}
