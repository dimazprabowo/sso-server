<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function clientApps(): BelongsToMany
    {
        return $this->belongsToMany(ClientApp::class, 'user_app_access')
            ->withPivot(['granted_at', 'granted_by'])
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasAppAccess(int $clientAppId): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->clientApps()->where('client_apps.id', $clientAppId)->exists();
    }

    public function hasAppAccessByOAuthClientId(string $oauthClientId): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->clientApps()
            ->where('client_apps.oauth_client_id', $oauthClientId)
            ->where('client_apps.is_active', true)
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
