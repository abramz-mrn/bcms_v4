<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // ...existing...

    public function markPaid(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'paid_at' => ['nullable','date'],
            'amount' => ['nullable','numeric','min:0'],
            'payment_method' => ['nullable','string','max:50'],     // e.g. cash, transfer, qris
            'reference_number' => ['nullable','string','max:100'],  // your column name
            'notes' => ['nullable','string'],
        ]);

        if ($invoice->status === 'Paid') {
            return response()->json([
                'message' => 'Invoice already paid',
                'code' => 'INVOICE_ALREADY_PAID',
            ], 409);
        }

        $user = $request->user();
        $old = $invoice->toArray();

        $payment = Payment::query()->create([
            'invoices_id' => $invoice->id,
            'paid_at' => isset($data['paid_at']) ? now()->parse($data['paid_at']) : now(),
            'amount' => $data['amount'] ?? $invoice->total, // default full
            'payment_method' => $data['payment_method'] ?? 'manual',
            'reference_number' => $data['reference_number'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user?->id,
        ]);

        $invoice->status = 'Paid';
        $invoice->save();

        AuditLog::query()->create([
            'users_id' => $user?->id,
            'users_name' => $user?->name,
            'ip_address' => $request->ip(),
            'action' => 'billing.invoice_mark_paid',
            'resource_type' => 'invoices',
            'old_value' => $old,
            'new_value' => [
                'invoice' => $invoice->toArray(),
                'payment' => $payment->toArray(),
            ],
            'description' => 'Invoice marked as paid (payment created)',
        ]);

        return response()->json([
            'invoice' => $invoice->load('payments'),
            'payment' => $payment,
        ]);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['subscription', 'payments']);
    }
}