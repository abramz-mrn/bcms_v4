<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkInvoicesOverdue extends Command
{
    protected $signature = 'bcms:mark-invoices-overdue {--date=} {--chunk=500}';
    protected $description = 'Mark unpaid invoices as Overdue when past due_date';

    public function handle(): int
    {
        $today = $this->option('date')
            ? Carbon::parse((string) $this->option('date'))->startOfDay()
            : now()->startOfDay();

        $chunk = (int) $this->option('chunk');

        // statuses that should NOT be changed
        $excluded = ['Paid', 'Void', 'Cancelled'];

        $totalUpdated = 0;

        Invoice::query()
            ->whereNull('deleted_at')
            ->whereNotIn('status', $excluded)
            ->whereDate('due_date', '<', $today->toDateString())
            ->where('status', '!=', 'Overdue')
            ->orderBy('id')
            ->chunkById($chunk, function ($invoices) use (&$totalUpdated) {
                foreach ($invoices as $inv) {
                    $old = $inv->toArray();

                    $inv->status = 'Overdue';
                    $inv->save();

                    $totalUpdated++;

                    // Optional audit log
                    AuditLog::query()->create([
                        'users_id' => null,
                        'users_name' => 'system',
                        'ip_address' => null,
                        'action' => 'billing.invoice_mark_overdue',
                        'resource_type' => 'invoices',
                        'old_value' => $old,
                        'new_value' => $inv->toArray(),
                        'description' => 'Invoice marked as overdue by scheduler',
                    ]);
                }
            });

        $this->info("Marked {$totalUpdated} invoices as Overdue.");
        return 0;
    }
}