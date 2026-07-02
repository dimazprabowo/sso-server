# SSO Server — OAuth 2.0 Provider

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/Livewire-4.x-purple?style=for-the-badge&logo=livewire" alt="Livewire">
  <img src="https://img.shields.io/badge/Passport-13.x-blue?style=for-the-badge" alt="Passport">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php" alt="PHP">
</p>

<p align="center">
  <strong>Single Sign-On Server dengan OAuth 2.0 Authorization Code Flow</strong><br>
  Centralized Authentication & Authorization System untuk Multiple Client Applications
</p>

---

## 📋 Daftar Isi

1. [Tentang SSO Server](#-tentang-sso-server)
2. [Fitur Utama](#-fitur-utama)
3. [Tech Stack](#-tech-stack)
4. [Instalasi](#-instalasi)
5. [Konfigurasi](#-konfigurasi)
6. [Database Schema](#-database-schema)
7. [Permissions & Roles](#-permissions--roles)
8. [API Endpoints](#-api-endpoints)
9. [User Sync Methods](#-user-sync-methods)
10. [Development](#-development)
11. [Production Deployment](#-production-deployment)
12. [Troubleshooting](#-troubleshooting)

---

## 🎯 Tentang SSO Server

SSO Server adalah aplikasi Laravel yang berfungsi sebagai **OAuth 2.0 Provider** menggunakan Laravel Passport. Aplikasi ini menyediakan:

- **Centralized Authentication** — Satu sistem login untuk semua aplikasi
- **OAuth 2.0 Authorization Code Flow** — Standar industri untuk SSO
- **User & Role Management** — Kelola user dan permission secara terpusat
- **Client App Management** — Daftarkan dan kelola aplikasi client
- **User Sync** — Sinkronisasi user ke client app via API atau Direct DB
- **Access Control** — Kontrol akses user per aplikasi

---

## ✨ Fitur Utama

### Authentication & Authorization
- ✅ Login dengan email & password
- ✅ Forgot password & reset via email
- ✅ Google reCAPTCHA v2 protection
- ✅ Session management & token revocation
- ✅ Login history tracking

### User Management
- ✅ CRUD users dengan Livewire
- ✅ Assign multiple roles per user
- ✅ Toggle active/inactive status
- ✅ Avatar upload support
- ✅ Email verification

### Role & Permission Management
- ✅ CRUD roles & permissions
- ✅ Permission-based access control
- ✅ Super admin bypass
- ✅ 16 permissions predefined

### Client App Management
- ✅ Register client apps untuk OAuth
- ✅ Auto-generate Client ID & Secret
- ✅ Regenerate secret key
- ✅ Configure sync method (OAuth Only, API, Direct DB)
- ✅ Toggle app active/inactive
- ✅ Route model binding dengan slug

### User Access Management
- ✅ Grant/revoke user access ke client apps
- ✅ Bulk assign users
- ✅ Access history tracking

### Remote App Sync
- ✅ Sync users ke client app via API
- ✅ Sync users via Direct DB connection
- ✅ Sync roles & permissions
- ✅ Real-time sync on user changes

---

## 🛠 Tech Stack

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Framework | Laravel | 12.x |
| PHP | PHP | 8.2+ |
| UI Framework | Livewire | 4.x |
| OAuth2 Provider | Laravel Passport | 13.x |
| RBAC | Spatie Permission | 6.x |
| Database | PostgreSQL / MySQL | 17 / 8 |
| CSS | TailwindCSS | 3.x |
| JS | Alpine.js | 3.x |
| Icons | Heroicons | 2.x |
| Security | reCAPTCHA v2 | - |

---

## 📦 Instalasi

### Prasyarat

- PHP 8.2 atau lebih tinggi
- Composer 2.x
- Node.js 18+ & npm
- PostgreSQL 17 atau MySQL 8
- Git

### Langkah Instalasi

```bash
# 1. Clone repository (jika dari git)
git clone <repository-url>
cd sso-server

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# Edit .env sesuai kebutuhan (lihat section Konfigurasi)

# 5. Generate Passport keys
php artisan passport:keys

# 6. Migrate & seed database
php artisan migrate:fresh --seed

# 7. Build assets
npm run build

# 8. Jalankan server
php artisan serve --port=8111
```

> **Penting:** Setelah seeder selesai, akan muncul output **Client ID** dan **Client Secret** untuk Boilerplate app. Salin kredensial ini ke `.env` client-app.

---

## ⚙️ Konfigurasi

### Environment Variables (.env)

```env
# Application
APP_NAME="SSO Server"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8111

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sso_server
DB_USERNAME=postgres
DB_PASSWORD=root

# Database (MySQL - Production)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sso_server
# DB_USERNAME=root
# DB_PASSWORD=

# Google reCAPTCHA v2 (Opsional)
RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key

# Mail (untuk reset password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@sso.company.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Port Default

- **Development:** `8111`
- **Production:** Sesuaikan dengan web server (nginx/apache)

---

## 🗄 Database Schema

### Tables Utama

| Table | Deskripsi | Kolom Penting |
|-------|-----------|---------------|
| `users` | User accounts | id, name, email, password, is_active |
| `client_apps` | Registered OAuth clients | id, slug, oauth_client_id, name, domain, sync_method |
| `user_app_access` | User access to apps | user_id, client_app_id, granted_at, granted_by |
| `login_histories` | Login tracking | user_id, client_app_id, ip_address, user_agent |
| `oauth_clients` | Passport OAuth clients | id, secret, redirect |
| `oauth_access_tokens` | Issued access tokens | id, user_id, client_id, expires_at |
| `roles` | Spatie roles | id, name, guard_name |
| `permissions` | Spatie permissions | id, name, guard_name |
| `model_has_roles` | User-role pivot | model_id, role_id |
| `role_has_permissions` | Role-permission pivot | role_id, permission_id |

### Migrations

Total: **14 migrations**

1. `create_users_table`
2. `create_password_reset_tokens_table`
3. `create_sessions_table`
4. `create_cache_table`
5. `create_jobs_table`
6. `create_permission_tables` (Spatie)
7. `create_passport_tables` (Passport)
8. `create_client_apps_table`
9. `create_user_app_access_table`
10. `create_login_histories_table`
11. `add_avatar_to_users_table`
12. `add_is_active_to_users_table`
13. `update_oauth_clients_table_for_passport_13`
14. `add_post_logout_redirect_uri_to_client_apps`

---

## 🔐 Permissions & Roles

### Permissions (16 total)

| Permission | Deskripsi |
|------------|----------|
| `dashboard_view` | Akses dashboard |
| `users_view` | Lihat daftar user |
| `users_create` | Tambah user baru |
| `users_update` | Edit user |
| `users_delete` | Hapus user |
| `roles_view` | Lihat daftar role |
| `roles_create` | Tambah role baru |
| `roles_update` | Edit role |
| `roles_delete` | Hapus role |
| `client_apps_view` | Lihat daftar client apps |
| `client_apps_create` | Tambah client app |
| `client_apps_update` | Edit client app |
| `client_apps_delete` | Hapus client app |
| `client_apps_manage` | Kelola user & role di remote app |
| `user_access_view` | Lihat user access |
| `user_access_update` | Grant/revoke user access |

### Roles Default

| Role | Permissions | Deskripsi |
|------|-------------|----------|
| `super-admin` | ALL | Full access (bypass semua permission check) |
| `admin` | users_*, roles_*, client_apps_*, user_access_* | Admin biasa |
| `user` | dashboard_view | User biasa |

### Test Accounts

| Email | Password | Role | Keterangan |
|-------|----------|------|------------|
| admin@company.com | password123 | super-admin | Full access |
| user@company.com | password123 | user | User biasa + akses Boilerplate |

---

## 🌐 API Endpoints

### OAuth 2.0 Endpoints (Laravel Passport)

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|----------|
| GET | `/oauth/authorize` | Session | Authorization request |
| POST | `/oauth/token` | None | Token exchange |
| POST | `/oauth/token/refresh` | None | Refresh token |

### API Endpoints

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|----------|
| GET | `/api/user` | Bearer | Get user profile + roles + apps |
| POST | `/api/logout` | Bearer | Revoke current token |
| POST | `/api/logout-all` | Bearer | Revoke all tokens + sessions |

### Response Example: GET /api/user

```json
{
  "id": 1,
  "name": "Administrator",
  "email": "admin@company.com",
  "avatar": null,
  "email_verified_at": "2026-03-06T00:00:00.000000Z",
  "is_active": true,
  "is_super_admin": true,
  "sso_roles": ["super-admin"],
  "sso_permissions": [
    "dashboard_view",
    "users_view",
    "users_create",
    "..."
  ],
  "apps": [
    {
      "id": 1,
      "name": "Boilerplate",
      "slug": "boilerplate-app",
      "domain": "http://localhost:8999"
    }
  ],
  "created_at": "2026-03-06T00:00:00.000000Z"
}
```

---

## 🔄 User Sync Methods

SSO Server mendukung 3 metode sinkronisasi user ke client app:

### 1. OAuth Only (No Sync)

- User hanya login via OAuth
- Tidak ada sinkronisasi data
- Client app hanya menerima token

**Konfigurasi:** Tidak perlu konfigurasi tambahan

### 2. API Sync

- SSO Server mengirim data user via REST API
- Client app menyediakan endpoint `/api/sso/*`
- Sync real-time saat user berubah

**Konfigurasi:**
```php
'sync_method' => 'api',
'api_base_url' => 'http://localhost:8999',
'api_secret_key' => 'shared-secret-key',
```

### 3. Direct DB

- SSO Server langsung menulis ke database client app
- Tidak perlu API endpoint
- Sync real-time via DB connection

**Konfigurasi:**
```php
'sync_method' => 'database',
'db_driver' => 'pgsql',
'db_host' => '127.0.0.1',
'db_port' => '5432',
'db_database' => 'client_app',
'db_username' => 'postgres',
'db_password' => 'password',
```

---

## 💻 Development

### Running Development Server

```bash
# Terminal 1: Laravel server
php artisan serve --port=8111

# Terminal 2: Vite dev server
npm run dev

# Terminal 3: Queue worker (untuk sync jobs)
php artisan queue:listen

# Terminal 4: Logs (opsional)
php artisan pail
```

### Atau gunakan composer script:

```bash
composer dev
# Menjalankan server, queue, logs, dan vite secara bersamaan
```

### Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Generate new Passport keys
php artisan passport:keys --force

# List all routes
php artisan route:list

# List all permissions
php artisan permission:show

# Reset database
php artisan migrate:fresh --seed
```

---

## 🚀 Production Deployment

### Pre-Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS for `APP_URL`
- [ ] Configure production database (MySQL recommended)
- [ ] Set strong `APP_KEY`
- [ ] Configure mail server (SMTP)
- [ ] Enable reCAPTCHA
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Setup queue worker (Supervisor)
- [ ] Setup SSL certificate
- [ ] Configure firewall

### Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sso.company.com

DB_CONNECTION=mysql
DB_HOST=your-mysql-host
DB_PORT=3306
DB_DATABASE=sso_server
DB_USERNAME=your-user
DB_PASSWORD=your-secure-password

RECAPTCHA_SITE_KEY=production-site-key
RECAPTCHA_SECRET_KEY=production-secret-key

MAIL_MAILER=smtp
MAIL_HOST=smtp.company.com
MAIL_PORT=587
MAIL_USERNAME=noreply@company.com
MAIL_PASSWORD=secure-password
MAIL_ENCRYPTION=tls
```

### Nginx Configuration Example

```nginx
server {
    listen 80;
    server_name sso.company.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name sso.company.com;
    root /var/www/sso-server/public;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔧 Troubleshooting

### `invalid_client` error saat OAuth

**Penyebab:** Client ID atau Secret tidak cocok

**Solusi:**
1. Jalankan `php artisan migrate:fresh --seed`
2. Salin ulang Client ID & Secret dari output seeder
3. Update `.env` client app
4. Restart kedua server

### 403 Forbidden saat login

**Penyebab:** User belum diberi akses ke client app

**Solusi:**
1. Login ke SSO Server sebagai admin
2. Buka menu **Akses User**
3. Grant akses user ke app yang dituju

### Passport keys not found

**Penyebab:** Passport keys belum di-generate

**Solusi:**
```bash
php artisan passport:keys
```

### Database connection error

**Penyebab:** Kredensial database salah atau service tidak running

**Solusi:**
1. Cek PostgreSQL/MySQL service: `systemctl status postgresql`
2. Verifikasi kredensial di `.env`
3. Test koneksi: `php artisan tinker` → `DB::connection()->getPdo()`

### reCAPTCHA validation failed

**Penyebab:** Site key atau secret key salah

**Solusi:**
1. Verifikasi keys di Google reCAPTCHA console
2. Update `.env` dengan keys yang benar
3. Clear config: `php artisan config:clear`

---

## 📚 Dokumentasi Terkait

- [Portal SSO README](../README.md) - Dokumentasi utama proyek
- [Client App README](../client-app/README.md) - Dokumentasi client app
- [Implementation Summary](../IMPLEMENTATION_SUMMARY.md) - Technical documentation
- [Panduan Implementasi](../PANDUAN_IMPLEMENTASI.md) - Panduan lengkap

---

## 📄 Lisensi

MIT License

---

## 🤝 Kontributor

Developed by PT. Biro Klasifikasi Indonesia Development Team

---

**Built with:** [Laravel](https://laravel.com) • [Livewire](https://livewire.laravel.com) • [Passport](https://laravel.com/docs/passport) • [Spatie Permission](https://spatie.be/docs/laravel-permission) • [TailwindCSS](https://tailwindcss.com) • [Alpine.js](https://alpinejs.dev)
