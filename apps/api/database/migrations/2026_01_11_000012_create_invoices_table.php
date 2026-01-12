<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('customers_id')->index();
            $table->unsignedBigInteger('subscriptions_id')->index();
            $table->unsignedBigInteger('products_id')->index();
            $table->date('period_start')->index();
            $table->date('period_end')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->date('due_date')->index();
            $table->string('status')->default('Unpaid')->index(); // Unpaid|Paid
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customers_id')->references('id')->on('customers');
            $table->foreign('subscriptions_id')->references('id')->on('subscriptions');
            $table->foreign('products_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};