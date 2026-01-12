<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('subscription_no')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('product_id')->constrained('products');

            $table->date('registration_date')->nullable();
            $table->text('installation_address')->nullable();

            $table->boolean('email_consent')->default(true);
            $table->boolean('sms_consent')->default(false);
            $table->boolean('whatsapp_consent')->default(false);

            $table->string('document_sf')->nullable();
            $table->string('document_asf')->nullable();
            $table->string('document_pks')->nullable();

            $table->string('status')->default('Registered')->index(); // Registered|Active|Soft-Limit|Suspend|Terminated
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};