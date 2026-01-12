<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('email', 'abramz@maroon-net.id')->first();

        $templates = [
            [
                'name' => 'Invoice Reminder (Email)',
                'type' => 'email',
                'subject' => 'Reminder Invoice {{invoice_no}}',
                'content' => "Dear {{customer_name}},\n\nThis is a reminder for invoice {{invoice_no}} amount {{total_amount}} due at {{due_date}}.\n\nThanks,\nMaroon-NET",
                'variables' => ['invoice_no','customer_name','total_amount','due_date'],
            ],
            [
                'name' => 'Invoice Reminder (SMS)',
                'type' => 'sms',
                'subject' => null,
                'content' => "Maroon-NET: Invoice {{invoice_no}} amount {{total_amount}} due {{due_date}}.",
                'variables' => ['invoice_no','total_amount','due_date'],
            ],
            [
                'name' => 'Invoice Reminder (WhatsApp)',
                'type' => 'whatsapp',
                'subject' => null,
                'content' => "Halo {{customer_name}}, reminder invoice {{invoice_no}} total {{total_amount}} jatuh tempo {{due_date}}. Terima kasih. -Maroon-NET",
                'variables' => ['customer_name','invoice_no','total_amount','due_date'],
            ],
        ];

        foreach ($templates as $t) {
            Template::updateOrCreate(
                ['name' => $t['name'], 'type' => $t['type']],
                [
                    'subject' => $t['subject'],
                    'content' => $t['content'],
                    'variables' => $t['variables'],
                    'is_active' => true,
                    'created_by' => $creator?->id,
                ]
            );
        }
    }
}