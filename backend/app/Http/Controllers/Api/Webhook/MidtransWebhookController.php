<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // TODO: verify signature key from Midtrans
        $payload = $request->all();

        // Expected mapping depends on Midtrans notification payload.
        // Starter: accept { invoice_no, transaction_id, gross_amount, transaction_status }
        $invoiceNo = $payload['invoice_no'] ?? null;

        if (!$invoiceNo) {
            return response()->json(['ok' => false, 'message' => 'Missing invoice_no'], 422);
        }

        $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
        if (!$invoice) {
            return response()->json(['ok' => false, 'message' => 'Invoice not found'], 404);
        }

        $status = (($payload['transaction_status'] ?? '') === 'settlement') ? 'verified' : 'pending';

        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_method' => 'virtual account',
            'payment_gateway' => 'Midtrans',
            'transaction_id' => $payload['transaction_id'] ?? null,
            'amount_paid' => (int)($payload['gross_amount'] ?? $invoice->total_amount),
            'paid_at' => now(),
            'ref_number' => $payload['order_id'] ?? null,
            'status' => $status,
            'notes' => 'Webhook Midtrans (stub)',
        ]);

        if ($status === 'verified') {
            $invoice->update(['status' => 'Paid']);
        }

        return response()->json(['ok' => true]);
    }
}