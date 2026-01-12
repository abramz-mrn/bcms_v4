<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_number')->unique();
            $table->unsignedBigInteger('customers_id')->index();
            $table->unsignedBigInteger('products_id')->index();
            $table->string('caller_name')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('category')->index(); // information|technical|billing|complaint
            $table->string('priority')->nullable()->index();
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open')->index();
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('sla_due_date')->nullable()->index();
            $table->text('resolution_notes')->nullable();
            $table->unsignedInteger('customer_rating')->nullable();
            $table->text('customer_feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customers_id')->references('id')->on('customers');
            $table->foreign('products_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};