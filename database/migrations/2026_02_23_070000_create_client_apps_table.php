<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_apps', function (Blueprint $table) {
            $table->id();
            $table->uuid('oauth_client_id')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain');
            $table->string('redirect_uri');
            $table->string('post_logout_redirect_uri')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('oauth_client_id')
                ->references('id')
                ->on('oauth_clients')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_apps');
    }
};
