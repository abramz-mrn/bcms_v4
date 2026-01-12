<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class XenditWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        // TODO: verify callback token header
        $payload = $request->all();

        // Starter: accept { invoice_no, id, amount, status }
        $invoiceNo = $payload['invoice_no'] ?? null;

        if (!$invoiceNo) {
            return response()->json(['ok' => false, 'message' => 'Missing invoice_no'], 422);
        }

        $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
        if (!$invoice) {
            return response()->json(['ok' => false, 'message' => 'Invoice not found'], 404);
        }

        $status = (($payload['status'] ?? '') === 'PAID') ? 'verified' : 'pending';

        Payment::create([
            'invoice_id' => $invoice->id,
            'payment_method' => 'virtual account',
            'payment_gateway' => 'Xendit',
            'transaction_id' => $payload['id'] ?? null,
            'amount_paid' => (int)($payload['amount'] ?? $invoice->total_amount),
            'paid_at' => now(),
            'ref_number' => $payload['external_id'] ?? null,
            'status' => $status,
            'notes' => 'Webhook Xendit (stub)',
        ]);

        if ($status === 'verified') {
            $invoice->update(['status' => 'Paid']);
        }

        return response()->json(['ok' => true]);
    }
}