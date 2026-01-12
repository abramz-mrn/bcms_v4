<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('user_name')->nullable();

            $table->string('ip_address')->nullable();

            $table->string('action')->index(); // create|update|delete|login|...
            $table->string('resource_type')->index();

            $table->jsonb('old_value')->nullable();
            $table->jsonb('new_value')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};