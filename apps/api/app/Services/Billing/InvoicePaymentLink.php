<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use Illuminate\Support\Str;

class InvoicePaymentLink
{
    public static function ensureToken(Invoice $invoice): Invoice
    {
        if ($invoice->payment_token && (!$invoice->payment_token_expires_at || $invoice->payment_token_expires_at->isFuture())) {
            return $invoice;
        }

        $invoice->payment_token = Str::random(48);
        // optional expiry: 90 days
        $invoice->payment_token_expires_at = now()->addDays(90);
        $invoice->save();

        return $invoice;
    }

    public static function url(Invoice $invoice): string
    {
        $webBase = rtrim(config('app.web_url', config('app.url')), '/'); // need WEB_URL
        return "{$webBase}/pay/invoice/{$invoice->payment_token}";
    }
}