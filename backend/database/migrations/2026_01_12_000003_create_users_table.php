<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('user_group_id')->constrained('user_groups');
            $table->foreignId('company_id')->nullable()->constrained('companies');

            $table->string('name');
            $table->string('password');
            $table->string('nik')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->unique();

            $table->string('locked')->default('active')->index(); // active|locked|inactive

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};