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
        $creator = User::where('email', 'abramz@maroon-net.id')->first();
        $invoice = Invoice::where('status','Unpaid')->first();
        if (!$invoice) return;

        $emailTpl = Template::where('type','email')->firstOrFail();
        $smsTpl = Template::where('type','sms')->firstOrFail();
        $waTpl = Template::where('type','whatsapp')->firstOrFail();

        $base = $invoice->due_date->copy()->startOfDay();

        $items = [
            ['tpl' => $emailTpl, 'channel' => 'email', 'trigger_type' => 'before_due', 'days_offset' => -3, 'scheduled_at' => $base->copy()->subDays(3)->addHours(9)],
            ['tpl' => $smsTpl, 'channel' => 'sms', 'trigger_type' => 'before_due', 'days_offset' => -1, 'scheduled_at' => $base->copy()->subDays(1)->addHours(9)],
            ['tpl' => $waTpl, 'channel' => 'whatsapp', 'trigger_type' => 'on_due', 'days_offset' => 0, 'scheduled_at' => $base->copy()->addHours(9)],
        ];

        foreach ($items as $it) {
            Reminder::create([
                'invoice_id' => $invoice->id,
                'template_id' => $it['tpl']->id,
                'channel' => $it['channel'],
                'trigger_type' => $it['trigger_type'],
                'days_offset' => $it['days_offset'],
                'scheduled_at' => $it['scheduled_at'],
                'status' => 'pending',
                'created_by' => $creator?->id,
            ]);
        }
    }
}