<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Passport\Client as PassportClientModel;

class ClientApp extends Model
{
    protected $fillable = [
        'oauth_client_id',
        'name',
        'slug',
        'domain',
        'redirect_uri',
        'post_logout_redirect_uri',
        'description',
        'is_active',
        'sync_method',
        'db_driver',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
        'api_base_url',
        'api_secret_key',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'db_host' => 'encrypted',
            'db_port' => 'encrypted',
            'db_database' => 'encrypted',
            'db_username' => 'encrypted',
            'db_password' => 'encrypted',
            'api_base_url' => 'encrypted',
            'api_secret_key' => 'encrypted',
        ];
    }

    public const SYNC_NONE = 'none';
    public const SYNC_DATABASE = 'database';
    public const SYNC_API = 'api';

    public const SYNC_METHODS = [
        self::SYNC_NONE,
        self::SYNC_DATABASE,
        self::SYNC_API,
    ];

    public const DB_DRIVERS = ['pgsql', 'mysql', 'sqlsrv', 'sqlite'];

    public function oauthClient(): BelongsTo
    {
        return $this->belongsTo(PassportClientModel::class, 'oauth_client_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_app_access')
            ->withPivot(['granted_at', 'granted_by'])
            ->withTimestamps();
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasUser(int $userId): bool
    {
        return $this->users()->where('users.id', $userId)->exists();
    }

    public function hasDatabaseConfig(): bool
    {
        return $this->sync_method === self::SYNC_DATABASE
            && $this->db_host && $this->db_database && $this->db_username;
    }

    public function hasApiConfig(): bool
    {
        return $this->sync_method === self::SYNC_API
            && $this->api_base_url && $this->api_secret_key;
    }

    public function getDatabaseConnectionConfig(): array
    {
        return [
            'driver' => $this->db_driver ?? 'pgsql',
            'host' => $this->db_host,
            'port' => $this->db_port ?? ($this->db_driver === 'mysql' ? '3306' : '5432'),
            'database' => $this->db_database,
            'username' => $this->db_username,
            'password' => $this->db_password ?? '',
            'charset' => $this->db_driver === 'mysql' ? 'utf8mb4' : 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
