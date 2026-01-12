<?php

namespace App\Console;

use App\Jobs\Automation\EnforceSoftLimitJob;
use App\Jobs\Automation\EnforceSuspendJob;
use App\Jobs\Automation\GenerateInvoicesJob;
use App\Jobs\Automation\ScheduleRemindersJob;
use App\Jobs\Automation\SendDueRemindersJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new GenerateInvoicesJob())->dailyAt('01:00');
        $schedule->job(new ScheduleRemindersJob())->hourly();

        // sender
        $schedule->job(new SendDueRemindersJob())->everyMinute();

        $schedule->job(new EnforceSoftLimitJob())->hourly();
        $schedule->job(new EnforceSuspendJob())->hourly();
    }

    protected function commands(): void
    {
        //
    }
}