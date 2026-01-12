<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Prevent double active subscriptions for same customer+product
        // Applies only when deleted_at IS NULL and status in (Active, Soft-Limit, Suspend)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS subscriptions_unique_active_customer_product
            ON subscriptions (customers_id, products_id)
            WHERE deleted_at IS NULL AND status IN ('Active', 'Soft-Limit', 'Suspend')
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS subscriptions_unique_active_customer_product");
    }
};