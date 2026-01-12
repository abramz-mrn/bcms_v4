<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('routers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('location')->nullable()->index();
            $table->text('description')->nullable();
            $table->string('ip_address')->index();
            $table->unsignedInteger('api_port')->default(8729);
            $table->unsignedInteger('ssh_port')->default(22);
            $table->string('api_username');
            $table->string('api_password');
            $table->text('api_certificate')->nullable();
            $table->boolean('tls_enabled')->default(true);
            $table->boolean('ssh_enabled')->default(false);
            $table->string('status')->default('offline')->index(); // online|offline|maintenance
            $table->unsignedInteger('sync_interval')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->json('config_backup')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};