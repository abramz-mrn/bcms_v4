<?php

namespace App\Jobs\Automation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnforceSoftLimitJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // Starter: check unpaid invoices and apply soft-limit.
    }
}