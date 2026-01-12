<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoices_id')->index();
            $table->string('payment_method')->index(); // cash|transfer|virtual account
            $table->string('payment_gateway')->nullable()->index(); // Midtrans|Xendit
            $table->string('transaction_id')->nullable()->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('fee', 14, 2)->default(0);
            $table->timestamp('paid_at')->nullable()->index();
            $table->string('reference_number')->nullable()->index();
            $table->string('document_proof')->nullable();
            $table->string('status')->default('pending')->index(); // pending|verified|rejected|refunded
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoices_id')->references('id')->on('invoices');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};