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

            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('subscription_id')->constrained('subscriptions');
            $table->foreignId('product_id')->constrained('products');

            $table->date('period_start')->index();
            $table->date('period_end')->index();

            $table->bigInteger('amount');
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('total_amount');

            $table->date('due_date')->index();
            $table->string('status')->default('Unpaid')->index(); // Unpaid|Paid

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};