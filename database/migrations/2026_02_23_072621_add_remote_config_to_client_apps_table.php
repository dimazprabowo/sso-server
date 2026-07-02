<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_apps', function (Blueprint $table) {
            $table->string('sync_method')->default('none')->after('is_active');
            $table->text('db_driver')->nullable()->after('sync_method');
            $table->text('db_host')->nullable()->after('db_driver');
            $table->text('db_port')->nullable()->after('db_host');
            $table->text('db_database')->nullable()->after('db_port');
            $table->text('db_username')->nullable()->after('db_database');
            $table->text('db_password')->nullable()->after('db_username');
            $table->text('api_base_url')->nullable()->after('db_password');
            $table->text('api_secret_key')->nullable()->after('api_base_url');
        });
    }

    public function down(): void
    {
        Schema::table('client_apps', function (Blueprint $table) {
            $table->dropColumn([
                'sync_method',
                'db_driver', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password',
                'api_base_url', 'api_secret_key',
            ]);
        });
    }
};
