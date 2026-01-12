<?php

namespace App\Jobs\Automation;

use App\Models\Subscription;
use App\Models\Invoice;
use App\Services\InvoiceNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateInvoicesJob implements ShouldQueue
{
    use Queueable;

    public function handle(InvoiceNumberService $invoiceNo): void
    {
        // Starter: no-op safe.
        // Next step: generate invoices by billing cycle + period start/end.
        // This skeleton ensures scheduler/queue wiring works.

        // Example placeholder: just ensure subscriptions query works
        Subscription::query()->limit(1)->get();

        // IMPORTANT: implement in next iteration (billing cycle)
    }
}