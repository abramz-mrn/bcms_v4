<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_groups_id')->nullable()->index();
            $table->unsignedBigInteger('companies_id')->nullable()->index();
            $table->string('nik')->nullable()->index();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('locked')->default('active')->index(); // active|locked|inactive

            $table->foreign('user_groups_id')->references('id')->on('user_groups');
            $table->foreign('companies_id')->references('id')->on('companies');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_groups_id']);
            $table->dropForeign(['companies_id']);
            $table->dropColumn(['user_groups_id','companies_id','nik','photo','phone','locked']);
        });
    }
};