<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'period_key')) {
                $table->string('period_key', 20)->nullable()->index(); // ex: 2026-01, 2026-W02
            }
        });

        // 1 invoice per subscription per period
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS invoices_unique_subscription_period
            ON invoices (subscriptions_id, period_key)
            WHERE deleted_at IS NULL AND period_key IS NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS invoices_unique_subscription_period");

        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'period_key')) {
                $table->dropColumn('period_key');
            }
        });
    }
};