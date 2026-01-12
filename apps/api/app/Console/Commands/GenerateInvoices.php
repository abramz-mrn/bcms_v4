<?php
// ...snip inside foreach...

                    $issueDate = \App\Services\Billing\InvoicePeriod::issueDate($billingCycle, $date)->startOfDay();
                    $dueDate = $issueDate->copy()->addDays(\App\Services\Billing\InvoicePeriod::dueDays($billingCycle));

                    // For ONETIME you might prefer issueDate=$date (today). If yes, keep as-is.
                    if ($billingCycle === 'One time charge') {
                        $issueDate = $date->copy();
                        $dueDate = $issueDate->copy()->addDays(\App\Services\Billing\InvoicePeriod::dueDays($billingCycle));
                    }

// ...snip...