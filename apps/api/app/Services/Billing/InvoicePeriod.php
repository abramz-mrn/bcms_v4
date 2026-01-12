<?php

namespace App\Services\Billing;

use Carbon\Carbon;

class InvoicePeriod
{
    public static function periodKey(string $billingCycle, Carbon $date): ?string
    {
        return match ($billingCycle) {
            'Monthly' => $date->format('Y-m'),
            'Weekly' => $date->format('o-\WW'),
            'Quarterly' => $date->format('Y') . '-Q' . (int) ceil($date->month / 3),
            'Semi-annually' => $date->format('Y') . '-S' . ($date->month <= 6 ? 1 : 2),
            'Annually' => $date->format('Y'),
            'One time charge' => 'ONETIME',
            default => null,
        };
    }

    public static function issueDate(string $billingCycle, Carbon $date): Carbon
    {
        return match ($billingCycle) {
            'Monthly' => $date->copy()->startOfMonth(),
            'Weekly' => $date->copy()->startOfWeek(), // Mon (ISO)
            'Quarterly' => $date->copy()->firstOfQuarter(),
            'Semi-annually' => $date->month <= 6
                ? $date->copy()->month(1)->startOfMonth()
                : $date->copy()->month(7)->startOfMonth(),
            'Annually' => $date->copy()->startOfYear(),
            'One time charge' => $date->copy(), // first time it runs will set issue date
            default => $date->copy(),
        };
    }

    public static function dueDays(string $billingCycle): int
    {
        return match ($billingCycle) {
            'Weekly' => 3,
            'Monthly' => 7,
            'Quarterly' => 10,
            'Semi-annually' => 14,
            'Annually' => 21,
            'One time charge' => 7,
            default => 7,
        };
    }
}