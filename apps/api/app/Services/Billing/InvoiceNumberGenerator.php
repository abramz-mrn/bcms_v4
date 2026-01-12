<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceNumberGenerator
{
    public static function generate(Carbon $date): string
    {
        $prefix = 'INV-' . $date->format('Ym') . '-';

        $count = Invoice::query()
            ->where('invoice_number', 'like', $prefix.'%')
            ->whereNull('deleted_at')
            ->count();

        $seq = str_pad((string) ($count + 1), 6, '0', STR_PAD_LEFT);

        return $prefix . $seq;
    }
}