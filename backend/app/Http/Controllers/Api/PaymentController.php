<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function index()
    {
        return Payment::query()
            ->with('invoice')
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store()
    {
        $data = request()->validate([
            'invoice_id' => ['required','exists:invoices,id'],
            'payment_method' => ['required','string','max:50'],
            'payment_gateway' => ['nullable','string','max:50'],
            'transaction_id' => ['nullable','string','max:100'],
            'amount_paid' => ['required','integer','min:0'],
            'paid_at' => ['nullable','date'],
            'ref_number' => ['nullable','string','max:100'],
            'status' => ['nullable','string','max:50'], // pending|verified|rejected|refunded
            'notes' => ['nullable','string'],
        ]);

        $data['created_by'] = request()->user()->id;
        $data['status'] = $data['status'] ?? 'pending';

        $payment = Payment::create($data);

        // If verified, mark invoice paid (starter behavior)
        if ($payment->status === 'verified') {
            $invoice = Invoice::find($payment->invoice_id);
            $invoice?->update(['status' => 'Paid']);
        }

        return $payment->load('invoice');
    }

    public function show(Payment $payment) { return $payment->load('invoice'); }

    public function update(Payment $payment)
    {
        $data = request()->validate([
            'status' => ['sometimes','string','max:50'],
            'notes' => ['nullable','string'],
            'paid_at' => ['nullable','date'],
            'ref_number' => ['nullable','string','max:100'],
        ]);

        $payment->update($data);

        if ($payment->status === 'verified') {
            $payment->invoice?->update(['status' => 'Paid']);
        }

        return $payment->load('invoice');
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $payment->delete();
        return response()->json(['ok' => true]);
    }
}