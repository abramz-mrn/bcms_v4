<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            'invoice.reminder.h-3' => 'H-3 sebelum jatuh tempo',
            'invoice.reminder.h-1' => 'H-1 sebelum jatuh tempo',
            'invoice.reminder.h+1' => 'H+1 setelah jatuh tempo',
            'invoice.reminder.h+3' => 'H+3 setelah jatuh tempo',
        ];

        foreach (['email', 'whatsapp', 'sms'] as $channel) {
            foreach ($events as $event => $label) {
                $exists = MessageTemplate::query()
                    ->where('channel', $channel)
                    ->where('event', $event)
                    ->whereNull('deleted_at')
                    ->exists();

                if ($exists) continue;

                $subject = null;
                $body = '';

                if ($channel === 'email') {
                    $subject = "{$label} - Invoice {{invoice_number}}";
                    $body = <<<HTML
<div style="font-family: Arial, sans-serif; line-height: 1.5;">
  <h2 style="margin:0 0 12px 0;">{$label}</h2>

  <p>Yth. <strong>{{customer_name}}</strong>,</p>
  <p>Ini adalah pengingat tagihan layanan Anda.</p>

  <table cellpadding="8" cellspacing="0" border="0" style="border-collapse: collapse; background:#fafafa; border:1px solid #e5e7eb;">
    <tr><td><strong>Invoice</strong></td><td>{{invoice_number}}</td></tr>
    <tr><td><strong>Periode</strong></td><td>{{period_key}}</td></tr>
    <tr><td><strong>Tanggal Terbit</strong></td><td>{{issue_date}}</td></tr>
    <tr><td><strong>Jatuh Tempo</strong></td><td>{{due_date}}</td></tr>
    <tr><td><strong>Total</strong></td><td>{{total}}</td></tr>
  </table>

  <p style="margin-top: 14px;">
    Mohon lakukan pembayaran tepat waktu untuk menghindari pembatasan layanan.
  </p>

  <p style="margin-top: 18px;">
    Terima kasih,<br/>
    <strong>Trira Inti Utama</strong>
  </p>
</div>
HTML;
                } elseif ($channel === 'whatsapp') {
                    // tetap text sederhana
                    $body = "[{$label}]\nYth {{customer_name}}, invoice {{invoice_number}} total {{total}} jatuh tempo {{due_date}}.";
                } else { // sms
                    $body = "[{$label}] {{customer_name}} invoice {{invoice_number}} total {{total}} jatuh tempo {{due_date}}.";
                }

                MessageTemplate::query()->create([
                    'key' => "{$channel}.{$event}",
                    'name' => strtoupper($channel) . " - {$label}",
                    'channel' => $channel,
                    'event' => $event,
                    'subject' => $subject,
                    'body' => $body,
                    'active' => true,
                    'meta' => null,
                ]);
            }
        }
    }
}