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
            $table->string('location')->nullable();
            $table->text('description')->nullable();

            $table->string('ip_address')->index();
            $table->integer('api_port')->default(8729);
            $table->integer('ssh_port')->default(22);

            $table->string('api_username');
            $table->string('api_password');
            $table->text('api_certificate')->nullable();

            $table->boolean('tls_enabled')->default(true);
            $table->boolean('ssh_enabled')->default(false);

            $table->string('status')->default('offline')->index(); // online|offline|maintenance
            $table->integer('sync_interval')->default(300);
            $table->timestampTz('last_sync_at')->nullable();

            $table->jsonb('config_backup')->default('{}');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ip_address', 'api_port']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};