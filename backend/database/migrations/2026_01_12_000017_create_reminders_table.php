<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('invoice_id')->constrained('invoices');
            $table->foreignId('template_id')->constrained('templates');

            $table->string('channel')->index(); // email|sms|whatsapp
            $table->string('trigger_type')->index(); // before_due|on_due|after_due|pre_soft_limit|pre_suspend
            $table->integer('days_offset')->default(0);

            $table->timestampTz('scheduled_at')->index();
            $table->timestampTz('sent_at')->nullable();

            $table->string('status')->default('pending')->index(); // pending|sent|failed|cancelled
            $table->text('error_message')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};