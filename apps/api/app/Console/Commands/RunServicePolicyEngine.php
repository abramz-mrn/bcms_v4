<?php

namespace App\Console\Commands;

use App\Jobs\ApplyServicePolicyJob;
use App\Models\Subscription;
use Illuminate\Console\Command;

class RunServicePolicyEngine extends Command
{
    protected $signature = 'bcms:service-policy-engine {--chunk=200}';
    protected $description = 'Dispatch service policy jobs (soft-limit/suspend/reactivate) per subscription';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        Subscription::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'Terminated')
            ->orderBy('id')
            ->chunk($chunk, function ($subs) {
                foreach ($subs as $s) {
                    ApplyServicePolicyJob::dispatch($s->id)->onQueue('automation');
                }
            });

        $this->info('Dispatched service policy jobs.');
        return 0;
    }
}