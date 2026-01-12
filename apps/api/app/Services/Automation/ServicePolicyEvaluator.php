<?php

namespace App\Services\Automation;

use App\Models\InternetService;
use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;

class ServicePolicyEvaluator
{
    public static function evaluate(Subscription $subscription, InternetService $internetService): array
    {
        // 1) Prefer OLDEST unpaid invoice (strict)
        $unpaid = Invoice::query()
            ->where('subscriptions_id', $subscription->id)
            ->whereNull('deleted_at')
            ->where('status', '!=', 'Paid')
            ->orderBy('due_date', 'asc')
            ->first();

        if ($unpaid) {
            $due = Carbon::parse($unpaid->due_date)->startOfDay();
            $today = now()->startOfDay();

            $daysPastDue = 0;
            if ($today->gt($due)) {
                $daysPastDue = $due->diffInDays($today);
            }

            $softN = (int) $internetService->auto_soft_limit;
            $suspendN = (int) $internetService->auto_suspend;

            if ($suspendN > 0 && $daysPastDue >= $suspendN) {
                return [
                    'action' => 'suspend',
                    'invoice_id' => $unpaid->id,
                    'days_past_due' => $daysPastDue,
                    'reason' => 'oldest_unpaid_over_suspend_threshold',
                ];
            }

            if ($softN > 0 && $daysPastDue >= $softN) {
                return [
                    'action' => 'soft_limit',
                    'invoice_id' => $unpaid->id,
                    'days_past_due' => $daysPastDue,
                    'reason' => 'oldest_unpaid_over_soft_threshold',
                ];
            }

            return [
                'action' => 'none',
                'invoice_id' => $unpaid->id,
                'days_past_due' => $daysPastDue,
                'reason' => 'oldest_unpaid_not_due_enough',
            ];
        }

        // 2) No unpaid invoice => reactivate (service should return to normal)
        return [
            'action' => 'reactivate',
            'invoice_id' => null,
            'reason' => 'no_unpaid_invoice',
        ];
    }
}