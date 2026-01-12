<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\InvoiceReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInvoiceReminders extends Command
{
    protected $signature = 'bcms:send-invoice-reminders {--date=} {--channel=log} {--chunk=500}';
    protected $description = 'Send invoice reminders on H-3, H-1, H+1, H+3 (idempotent)';

    public function handle(): int
    {
        $today = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : now()->startOfDay();

        $channel = (string) $this->option('channel');
        $chunk = (int) $this->option('chunk');

        // Mapping: offset days from due_date to today -> reminder day_offset to store
        // offset = due_date - today
        $targets = [
            3 => -3,   // H-3
            1 => -1,   // H-1
            -1 => 1,   // H+1
            -3 => 3,   // H+3
        ];

        $excluded = ['Paid', 'Void', 'Cancelled'];

        $sent = 0;
        $skipped = 0;

        Invoice::query()
            ->whereNull('deleted_at')
            ->whereNotIn('status', $excluded)
            ->whereNotNull('due_date')
            ->orderBy('id')
            ->chunkById($chunk, function ($invoices) use ($today, $targets, $channel, &$sent, &$skipped) {
                foreach ($invoices as $inv) {
                    $due = Carbon::parse($inv->due_date)->startOfDay();
                    $offset = $due->diffInDays($today, false); // today - due (signed)
                    // We want due - today:
                    $offset = -$offset;

                    if (!array_key_exists($offset, $targets)) {
                        continue;
                    }

                    $dayOffset = $targets[$offset];
                    $exists = InvoiceReminder::query()
                        ->where('invoices_id', $inv->id)
                        ->where('channel', $channel)
                        ->where('day_offset', $dayOffset)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    $message = $this->buildMessage($inv, $dayOffset);

                    // For starter: "send" = store + audit log (real channel later)
                    $rem = InvoiceReminder::query()->create([
                        'invoices_id' => $inv->id,
                        'channel' => $channel,
                        'day_offset' => $dayOffset,
                        'scheduled_for' => $today->toDateString(),
                        'sent_at' => now(),
                        'status' => 'sent',
                        'message' => $message,
                        'meta' => [
                            'invoice_number' => $inv->invoice_number,
                            'total' => $inv->total,
                            'due_date' => $inv->due_date,
                        ],
                    ]);

                    AuditLog::query()->create([
                        'users_id' => null,
                        'users_name' => 'system',
                        'ip_address' => null,
                        'action' => 'billing.invoice_reminder_sent',
                        'resource_type' => 'invoices',
                        'old_value' => null,
                        'new_value' => [
                            'invoice_id' => $inv->id,
                            'invoice_number' => $inv->invoice_number,
                            'channel' => $channel,
                            'day_offset' => $dayOffset,
                            'reminder_id' => $rem->id,
                        ],
                        'description' => 'Invoice reminder sent (log channel)',
                    ]);

                    $sent++;
                }
            });

        $this->info("Reminder result: sent={$sent}, skipped(existing)={$skipped}");
        return 0;
    }

    private function buildMessage($inv, int $dayOffset): string
    {
        // dayOffset: -3/-1/+1/+3 relative to due date
        $when = match ($dayOffset) {
            -3 => 'H-3 sebelum jatuh tempo',
            -1 => 'H-1 sebelum jatuh tempo',
            1 => 'H+1 setelah jatuh tempo',
            3 => 'H+3 setelah jatuh tempo',
            default => 'reminder',
        };

        $no = $inv->invoice_number ?? ('#'.$inv->id);
        return "Reminder {$when}: Invoice {$no} total {$inv->total} jatuh tempo {$inv->due_date}.";
    }
}