<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('provisionings', function (Blueprint $table) {
            $table->string('service_state')->default('normal')->index(); // normal|soft_limit|suspend
            $table->string('original_rate_limit')->nullable(); // PPPoE baseline (e.g. 10M/10M)
            $table->string('original_max_limit')->nullable();  // Static baseline (e.g. 10M/10M)
        });
    }

    public function down(): void
    {
        Schema::table('provisionings', function (Blueprint $table) {
            $table->dropColumn(['service_state','original_rate_limit','original_max_limit']);
        });
    }
};