<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customers_id')->index();
            $table->unsignedBigInteger('products_id')->index();
            $table->date('registration_date')->index();
            $table->boolean('email_consent')->default(true);
            $table->boolean('sms_consent')->default(false);
            $table->boolean('whatsapp_consent')->default(false);
            $table->string('document_sf')->nullable();
            $table->string('document_asf')->nullable();
            $table->string('document_pks')->nullable();
            $table->string('status')->default('Registered')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customers_id')->references('id')->on('customers');
            $table->foreign('products_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};