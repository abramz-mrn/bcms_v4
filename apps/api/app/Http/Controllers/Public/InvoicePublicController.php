<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoicePublicController extends Controller
{
    public function showByToken(Request $request, string $token)
    {
        $invoice = Invoice::query()
            ->where('payment_token', $token)
            ->whereNull('deleted_at')
            ->firstOrFail();

        if ($invoice->payment_token_expires_at && $invoice->payment_token_expires_at->isPast()) {
            return response()->json(['message' => 'Payment link expired'], 410);
        }

        // Return only safe fields
        return response()->json([
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'issue_date' => $invoice->issue_date,
            'due_date' => $invoice->due_date,
            'total' => $invoice->total,
            'period_key' => $invoice->period_key ?? null,
            'notes' => $invoice->notes ?? null,
        ]);
    }
}