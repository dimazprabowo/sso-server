<?php

namespace App\Livewire;

use App\Livewire\Traits\HasDynamicLike;
use App\Livewire\Traits\HasNotification;
use App\Models\ClientApp;
use App\Services\ClientAppService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Client Apps'])]
#[Title('Client Apps')]
class ClientAppManager extends Component
{
    use WithPagination, HasNotification, HasDynamicLike;

    public string $search = '';
    public string $isActiveFilter = '';
    public bool $filterChanged = false;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showSecretModal = false;
    public bool $showRegenerateModal = false;
    public bool $isEditing = false;

    public ?int $editingAppId = null;
    public ?int $deletingAppId = null;
    public string $deletingAppName = '';
    public ?int $regeneratingAppId = null;
    public string $regeneratingAppName = '';
    public string $revealedSecret = '';
    public string $revealedClientId = '';

    // Form fields
    public string $name = '';
    public string $domain = '';
    public string $redirect_uri = '';
    public string $post_logout_redirect_uri = '';
    public string $description = '';
    public bool $is_active = true;

    // Remote config
    public string $sync_method = 'none';
    public string $db_driver = 'pgsql';
    public string $db_host = '';
    public string $db_port = '';
    public string $db_database = '';
    public string $db_username = '';
    public string $db_password = '';
    public string $api_base_url = '';
    public string $api_secret_key = '';

    // Test connection result
    public string $testConnectionResult = '';
    public string $testConnectionStatus = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatedIsActiveFilter(): void
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function getIsActiveOptionsProperty(): array
    {
        return [
            ['value' => '1', 'label' => 'Aktif'],
            ['value' => '0', 'label' => 'Nonaktif'],
        ];
    }

    public function resetFilters(): void
    {
        $this->isActiveFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function openCreateModal(): void
    {
        Gate::authorize('create', ClientApp::class);
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $appId): void
    {
        $app = ClientApp::findOrFail($appId);
        Gate::authorize('update', $app);
        $this->editingAppId = $app->id;
        $this->name = $app->name;
        $this->domain = $app->domain;
        $this->redirect_uri = $app->redirect_uri;
        $this->post_logout_redirect_uri = $app->post_logout_redirect_uri ?? '';
        $this->description = $app->description ?? '';
        $this->is_active = $app->is_active;

        $this->sync_method = $app->sync_method ?? 'none';
        $this->db_driver = $app->db_driver ?? 'pgsql';
        $this->db_host = $app->db_host ?? '';
        $this->db_port = $app->db_port ?? '';
        $this->db_database = $app->db_database ?? '';
        $this->db_username = $app->db_username ?? '';
        $this->db_password = $app->db_password ?? '';
        $this->api_base_url = $app->api_base_url ?? '';
        $this->api_secret_key = $app->api_secret_key ?? '';

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save(ClientAppService $service): void
    {
        $this->isEditing
            ? Gate::authorize('update', ClientApp::findOrFail($this->editingAppId))
            : Gate::authorize('create', ClientApp::class);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255'],
            'redirect_uri' => ['required', 'url', 'max:500'],
            'post_logout_redirect_uri' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sync_method' => ['required', 'in:none,database,api'],
        ];

        if ($this->sync_method === 'database') {
            $rules['db_driver'] = ['required', 'in:pgsql,mysql,sqlsrv,sqlite'];
            $rules['db_host'] = ['required', 'string', 'max:255'];
            $rules['db_port'] = ['required', 'string', 'max:10'];
            $rules['db_database'] = ['required', 'string', 'max:255'];
            $rules['db_username'] = ['required', 'string', 'max:255'];
            $rules['db_password'] = ['nullable', 'string', 'max:255'];
        }

        if ($this->sync_method === 'api') {
            $rules['api_base_url'] = ['required', 'url', 'max:500'];
            $rules['api_secret_key'] = ['required', 'string', 'max:255'];
        }

        try {
            $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        $data = [
            'name' => $this->name,
            'domain' => $this->domain,
            'redirect_uri' => $this->redirect_uri,
            'post_logout_redirect_uri' => $this->post_logout_redirect_uri ?: null,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
            'sync_method' => $this->sync_method,
            'db_driver' => $this->db_driver ?: null,
            'db_host' => $this->db_host ?: null,
            'db_port' => $this->db_port ?: null,
            'db_database' => $this->db_database ?: null,
            'db_username' => $this->db_username ?: null,
            'db_password' => $this->db_password ?: null,
            'api_base_url' => $this->api_base_url ?: null,
            'api_secret_key' => $this->api_secret_key ?: null,
        ];

        if ($this->isEditing) {
            $app = ClientApp::findOrFail($this->editingAppId);
            $service->update($app, $data);
            $this->notifySuccess('Client app berhasil diperbarui.');
        } else {
            $result = $service->create($data);
            $this->revealedClientId = $result['client_id'];
            $this->revealedSecret = $result['client_secret'];
            $this->showModal = false;
            $this->showSecretModal = true;
            $this->resetForm();
            return;
        }

        $this->closeModal();
    }

    public function confirmDelete(int $appId): void
    {
        $app = ClientApp::findOrFail($appId);
        Gate::authorize('delete', $app);
        $this->deletingAppId = $app->id;
        $this->deletingAppName = $app->name;
        $this->showDeleteModal = true;
    }

    public function deleteApp(ClientAppService $service): void
    {
        $app = ClientApp::findOrFail($this->deletingAppId);
        Gate::authorize('delete', $app);
        $service->delete($app);

        $this->showDeleteModal = false;
        $this->deletingAppId = null;
        $this->deletingAppName = '';
        $this->notifySuccess('Client app berhasil dihapus.');
    }

    public function toggleActive(int $appId, ClientAppService $service): void
    {
        $app = ClientApp::findOrFail($appId);
        Gate::authorize('toggleActive', $app);
        $service->toggleActive($app);
        $this->notifySuccess("Status {$app->name} berhasil diubah.");
    }

    public function confirmRegenerate(int $appId): void
    {
        $app = ClientApp::findOrFail($appId);
        Gate::authorize('regenerateSecret', $app);
        $this->regeneratingAppId = $app->id;
        $this->regeneratingAppName = $app->name;
        $this->showRegenerateModal = true;
    }

    public function regenerateSecret(ClientAppService $service): void
    {
        $app = ClientApp::findOrFail($this->regeneratingAppId);
        Gate::authorize('regenerateSecret', $app);
        $newSecret = $service->regenerateSecret($app);

        $this->showRegenerateModal = false;
        $this->regeneratingAppId = null;
        $this->regeneratingAppName = '';

        if ($newSecret) {
            $this->revealedClientId = $app->oauth_client_id;
            $this->revealedSecret = $newSecret;
            $this->showSecretModal = true;
        }
    }

    public function closeRegenerateModal(): void
    {
        $this->showRegenerateModal = false;
        $this->regeneratingAppId = null;
        $this->regeneratingAppName = '';
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function closeSecretModal(): void
    {
        $this->showSecretModal = false;
        $this->revealedSecret = '';
        $this->revealedClientId = '';
    }

    public function testDatabaseConnection(): void
    {
        Gate::authorize('testConnection', ClientApp::class);
        $this->testConnectionResult = '';
        $this->testConnectionStatus = '';

        try {
            $config = [
                'driver' => $this->db_driver ?: 'pgsql',
                'host' => $this->db_host,
                'port' => $this->db_port ?: ($this->db_driver === 'mysql' ? '3306' : '5432'),
                'database' => $this->db_database,
                'username' => $this->db_username,
                'password' => $this->db_password,
                'charset' => $this->db_driver === 'mysql' ? 'utf8mb4' : 'utf8',
                'prefix' => '',
                'schema' => 'public',
            ];

            config(['database.connections._test_remote' => $config]);
            \DB::connection('_test_remote')->getPdo();
            \DB::disconnect('_test_remote');

            $this->testConnectionResult = 'Koneksi database berhasil!';
            $this->testConnectionStatus = 'success';
        } catch (\Exception $e) {
            $this->testConnectionResult = 'Gagal: ' . $e->getMessage();
            $this->testConnectionStatus = 'error';
        }
    }

    public function testApiConnection(): void
    {
        Gate::authorize('testConnection', ClientApp::class);
        $this->testConnectionResult = '';
        $this->testConnectionStatus = '';

        try {
            $response = \Http::timeout(10)
                ->withHeaders([
                    'X-SSO-Secret' => $this->api_secret_key,
                    'Accept' => 'application/json',
                ])
                ->get(rtrim($this->api_base_url, '/') . '/sso/ping');

            if ($response->successful()) {
                $this->testConnectionResult = 'Koneksi API berhasil!';
                $this->testConnectionStatus = 'success';
            } else {
                $this->testConnectionResult = 'Gagal: HTTP ' . $response->status();
                $this->testConnectionStatus = 'error';
            }
        } catch (\Exception $e) {
            $this->testConnectionResult = 'Gagal: ' . $e->getMessage();
            $this->testConnectionStatus = 'error';
        }
    }

    public function updatedSyncMethod(): void
    {
        $this->testConnectionResult = '';
        $this->testConnectionStatus = '';
    }

    private function resetForm(): void
    {
        $this->editingAppId = null;
        $this->name = '';
        $this->domain = '';
        $this->redirect_uri = '';
        $this->post_logout_redirect_uri = '';
        $this->description = '';
        $this->is_active = true;
        $this->sync_method = 'none';
        $this->db_driver = 'pgsql';
        $this->db_host = '';
        $this->db_port = '';
        $this->db_database = '';
        $this->db_username = '';
        $this->db_password = '';
        $this->api_base_url = '';
        $this->api_secret_key = '';
        $this->testConnectionResult = '';
        $this->testConnectionStatus = '';
    }

    public function render()
    {
        Gate::authorize('viewAny', ClientApp::class);

        $query = ClientApp::with('oauthClient');

        if ($this->search) {
            $operator = $this->getLikeOperator();
            $query->where(function ($q) use ($operator) {
                $q->where('name', $operator, "%{$this->search}%")
                  ->orWhere('domain', $operator, "%{$this->search}%");
            });
        }

        if ($this->isActiveFilter !== '') {
            $query->where('is_active', $this->isActiveFilter === '1');
        }

        $apps = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($this->filterChanged) {
            $this->notifySuccess("Ditemukan {$apps->total()} data aplikasi.");
            $this->filterChanged = false;
        }

        return view('livewire.client-app-manager', compact('apps'));
    }
}
