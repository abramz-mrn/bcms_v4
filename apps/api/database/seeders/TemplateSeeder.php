<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->where('name','Abramz')->firstOrFail();

        Template::query()->create([
            'name' => 'Reminder H-3 Email',
            'type' => 'email',
            'subject' => 'Pengingat Pembayaran Invoice {{invoice_no}}',
            'content' => "Yth {{customer_name}},\n\nIni pengingat pembayaran untuk invoice {{invoice_no}} total {{total_amount}} jatuh tempo {{due_date}}.\n\nTerima kasih,\nMaroon-NET",
            'variables' => ['invoice_no','customer_name','total_amount','due_date'],
            'is_active' => true,
            'created_by' => $creator->id,
        ]);

        Template::query()->create([
            'name' => 'Reminder H-1 SMS',
            'type' => 'sms',
            'subject' => null,
            'content' => "Maroon-NET: Invoice {{invoice_no}} total {{total_amount}} jatuh tempo {{due_date}}. Abaikan bila sudah bayar.",
            'variables' => ['invoice_no','total_amount','due_date'],
            'is_active' => true,
            'created_by' => $creator->id,
        ]);

        Template::query()->create([
            'name' => 'Reminder H+1 WhatsApp',
            'type' => 'whatsapp',
            'subject' => null,
            'content' => "Halo {{customer_name}}, invoice {{invoice_no}} sebesar {{total_amount}} sudah lewat jatuh tempo ({{due_date}}). Mohon segera melakukan pembayaran.",
            'variables' => ['customer_name','invoice_no','total_amount','due_date'],
            'is_active' => true,
            'created_by' => $creator->id,
        ]);
    }
}