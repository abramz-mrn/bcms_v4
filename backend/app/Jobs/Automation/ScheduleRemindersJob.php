<?php

namespace App\Jobs\Automation;

use App\Models\InternetService;
use App\Models\Invoice;
use App\Models\Reminder;
use App\Models\Template;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ScheduleRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // pick templates (first active per type)
        $tplEmail = Template::query()->where('type','email')->where('is_active',true)->orderBy('id')->first();
        $tplSms = Template::query()->where('type','sms')->where('is_active',true)->orderBy('id')->first();
        $tplWa = Template::query()->where('type','whatsapp')->where('is_active',true)->orderBy('id')->first();

        // Load unpaid invoices in a reasonable time window to schedule reminders.
        // We'll schedule for due_date +/- 14 days (safe window).
        $from = now()->subDays(14)->toDateString();
        $to = now()->addDays(60)->toDateString();

        $invoices = Invoice::query()
            ->where('status', 'Unpaid')
            ->whereBetween('due_date', [$from, $to])
            ->with(['customer','subscription.product'])
            ->orderBy('due_date')
            ->limit(500)
            ->get();

        foreach ($invoices as $invoice) {
            $customer = $invoice->customer;
            $sub = $invoice->subscription;
            if (!$customer || !$sub) continue;

            // get internet service policy by product_id (for soft_limit/suspend days)
            $policy = InternetService::query()
                ->where('product_id', $invoice->product_id)
                ->first();

            $autoSoft = $policy?->auto_soft_limit ?? 3;
            $autoSuspend = $policy?->auto_suspend ?? 7;

            $rules = [
                // days_offset relative to due_date
                ['trigger_type' => 'before_due', 'days_offset' => -7],
                ['trigger_type' => 'before_due', 'days_offset' => -3],
                ['trigger_type' => 'before_due', 'days_offset' => -1],
                ['trigger_type' => 'on_due', 'days_offset' => 0],
                ['trigger_type' => 'after_due', 'days_offset' => 1],
                ['trigger_type' => 'after_due', 'days_offset' => 3],
                ['trigger_type' => 'pre_soft_limit', 'days_offset' => max($autoSoft - 1, 0)],
                ['trigger_type' => 'pre_suspend', 'days_offset' => max($autoSuspend - 1, 0)],
            ];

            // Decide channels based on consent + basic availability
            $channels = [];

            if ($sub->email_consent && $customer->email && $tplEmail) {
                $channels[] = ['channel' => 'email', 'template_id' => $tplEmail->id];
            }
            if ($sub->sms_consent && $customer->phone && $tplSms) {
                $channels[] = ['channel' => 'sms', 'template_id' => $tplSms->id];
            }
            if ($sub->whatsapp_consent && $customer->phone && $tplWa) {
                $channels[] = ['channel' => 'whatsapp', 'template_id' => $tplWa->id];
            }

            foreach ($channels as $ch) {
                foreach ($rules as $r) {
                    $scheduledAt = $invoice->due_date
                        ->copy()
                        ->startOfDay()
                        ->addDays((int)$r['days_offset'])
                        ->addHours(9); // send at 09:00

                    $this->createReminderIdempotent(
                        invoiceId: $invoice->id,
                        templateId: $ch['template_id'],
                        channel: $ch['channel'],
                        triggerType: $r['trigger_type'],
                        daysOffset: (int)$r['days_offset'],
                        scheduledAt: $scheduledAt
                    );
                }
            }
        }
    }

    private function createReminderIdempotent(
        int $invoiceId,
        int $templateId,
        string $channel,
        string $triggerType,
        int $daysOffset,
        $scheduledAt
    ): void {
        DB::transaction(function () use ($invoiceId, $templateId, $channel, $triggerType, $daysOffset, $scheduledAt) {
            // Using unique constraint + upsert for idempotency
            Reminder::query()->updateOrCreate(
                [
                    'invoice_id' => $invoiceId,
                    'channel' => $channel,
                    'trigger_type' => $triggerType,
                    'days_offset' => $daysOffset,
                ],
                [
                    'template_id' => $templateId,
                    'scheduled_at' => $scheduledAt,
                    'status' => 'pending',
                    'error_message' => null,
                ]
            );
        });
    }
}