<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80);                 // unique logical key
            $table->string('name', 120);               // human name
            $table->string('channel', 20);             // email|whatsapp|sms
            $table->string('event', 80);               // invoice.reminder.h-3, etc
            $table->string('subject', 200)->nullable(); // email only
            $table->text('body');                      // main content
            $table->boolean('active')->default(true);
            $table->json('meta')->nullable();          // optional (sender name, etc)
            $table->timestamps();
            $table->softDeletes();

            $table->index(['channel','event']);
        });

        // Enforce unique template per channel+event (soft delete aware)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS message_templates_unique_channel_event
            ON message_templates (channel, event)
            WHERE deleted_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};