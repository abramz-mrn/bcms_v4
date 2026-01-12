<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['customers_id', 'status'], 'subscriptions_customers_status_idx');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['status', 'sla_due_date'], 'tickets_status_sla_due_idx');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_customers_status_idx');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_status_sla_due_idx');
        });
    }
};