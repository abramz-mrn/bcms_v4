<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoices_id')->constrained('invoices');
            $table->string('channel', 20)->default('log'); // log/email/sms/whatsapp
            $table->integer('day_offset'); // -3, -1, +1, +3
            $table->date('scheduled_for'); // the day it was supposed to send
            $table->timestamp('sent_at')->nullable();
            $table->string('status', 20)->default('sent'); // sent/failed/skipped
            $table->text('message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Idempotency: one reminder per invoice+offset+channel (non-deleted)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS invoice_reminders_unique
            ON invoice_reminders (invoices_id, channel, day_offset)
            WHERE deleted_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_reminders');
    }
};