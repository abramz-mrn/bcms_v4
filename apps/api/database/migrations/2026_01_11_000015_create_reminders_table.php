<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoices_id')->index();
            $table->unsignedBigInteger('templates_id')->index();
            $table->string('channel')->index(); // email|sms|whatsapp
            $table->string('trigger_type')->index(); // before_due|on_due|after_due|pre_soft_limit|pre_suspend
            $table->integer('days_offset')->default(0); // -7, -3, -1, +1, etc
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->string('status')->default('pending')->index(); // pending|sent|failed|cancelled
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('invoices_id')->references('id')->on('invoices');
            $table->foreign('templates_id')->references('id')->on('templates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};