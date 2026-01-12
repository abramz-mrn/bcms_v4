<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('invoice_id')->constrained('invoices');

            $table->string('payment_method')->index(); // cash|transfer|virtual account
            $table->string('payment_gateway')->nullable()->index(); // Midtrans|Xendit
            $table->string('transaction_id')->nullable()->index();

            $table->bigInteger('amount_paid');
            $table->timestampTz('paid_at')->nullable();

            $table->string('ref_number')->nullable();
            $table->string('document_proof')->nullable();

            $table->string('status')->default('pending')->index(); // pending|verified|rejected|refunded
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};