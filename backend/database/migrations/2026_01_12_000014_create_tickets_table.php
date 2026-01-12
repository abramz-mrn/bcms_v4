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

            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('product_id')->nullable()->constrained('products');

            $table->string('caller_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('category')->index();
            $table->string('priority')->nullable()->index();
            $table->string('subject');
            $table->text('description')->nullable();

            $table->string('status')->default('open')->index(); // open|assigned|in progress|resolved|closed
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestampTz('assigned_at')->nullable();
            $table->timestampTz('resolved_at')->nullable();
            $table->timestampTz('closed_at')->nullable();

            $table->timestampTz('sla_due_date')->nullable()->index();
            $table->text('resolution_notes')->nullable();

            $table->integer('customer_rating')->nullable();
            $table->text('customer_feedback')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};