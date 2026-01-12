<?php

namespace App\Jobs\Automation;

use App\Models\Reminder;
use App\Services\Notifications\NotificationRouter;
use App\Services\Templates\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendDueRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(
        NotificationRouter $router,
        TemplateRenderer $renderer
    ): void {
        // process in small batches
        $now = now();

        // Locking strategy: select IDs first, then lock rows to avoid double-send
        $ids = Reminder::query()
            ->whereIn('status', ['pending'])
            ->where('scheduled_at', '<=', $now)
            ->orderBy('scheduled_at')
            ->limit(50)
            ->pluck('id')
            ->all();

        foreach ($ids as $id) {
            DB::transaction(function () use ($id, $router, $renderer) {
                $reminder = Reminder::query()->lockForUpdate()->find($id);
                if (!$reminder) return;
                if ($reminder->status !== 'pending') return;

                $reminder->load(['invoice.customer', 'invoice.subscription', 'template']);

                $invoice = $reminder->invoice;
                $template = $reminder->template;

                if (!$invoice || !$template || !$invoice->customer || !$invoice->subscription) {
                    $reminder->update([
                        'status' => 'failed',
                        'error_message' => 'Missing invoice/customer/subscription/template relation',
                    ]);
                    return;
                }

                $customer = $invoice->customer;
                $sub = $invoice->subscription;

                $vars = [
                    'invoice_no' => $invoice->invoice_no,
                    'customer_name' => $customer->name,
                    'total_amount' => (string) $invoice->total_amount,
                    'due_date' => $invoice->due_date?->format('Y-m-d'),
                ];

                $subject = $template->subject ? $renderer->render($template->subject, $vars) : '';
                $content = $renderer->render($template->content, $vars);

                try {
                    $result = match ($reminder->channel) {
                        'email' => $router->sendEmailIfAllowed($sub, $customer, $subject, $content),
                        'sms' => $router->sendSmsIfAllowed($sub, $customer, $content),
                        'whatsapp' => $router->sendWhatsappIfAllowed($sub, $customer, $content),
                        default => ['status' => 'failed', 'reason' => 'unknown_channel'],
                    };

                    if (($result['status'] ?? '') === 'sent') {
                        $reminder->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                            'error_message' => null,
                        ]);
                        return;
                    }

                    // whatsapp may return sent_or_skipped_by_driver; treat as sent unless driver skipped
                    if (($result['status'] ?? '') === 'sent_or_skipped_by_driver') {
                        // We can't know if skipped unless driver throws; log as sent (accepted)
                        $reminder->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                            'error_message' => null,
                        ]);
                        return;
                    }

                    if (($result['status'] ?? '') === 'skipped') {
                        $reminder->update([
                            'status' => 'skipped',
                            'error_message' => 'skipped: '.($result['reason'] ?? 'unknown'),
                        ]);
                        return;
                    }

                    $reminder->update([
                        'status' => 'failed',
                        'error_message' => $result['reason'] ?? 'failed',
                    ]);
                } catch (\Throwable $e) {
                    Log::error('[REMINDER] send failed', ['id' => $reminder->id, 'error' => $e->getMessage()]);
                    $reminder->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                    ]);
                }
            });
        }
    }
}