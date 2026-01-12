<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Reminder;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->where('name','Fandi')->firstOrFail();
        $invoice = Invoice::query()->where('invoice_no','INV-2026-0001')->firstOrFail();
        $tpl = Template::query()->where('name','Reminder H-3 Email')->firstOrFail();

        Reminder::query()->create([
            'invoices_id' => $invoice->id,
            'templates_id' => $tpl->id,
            'channel' => 'email',
            'trigger_type' => 'before_due',
            'days_offset' => -3,
            'scheduled_at' => now()->addMinutes(10),
            'status' => 'pending',
            'created_by' => $creator->id,
        ]);
    }
}