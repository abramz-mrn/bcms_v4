<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index() { return Payment::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'invoices_id' => ['required','integer','exists:invoices,id'],
            'payment_method' => ['required','string'],
            'payment_gateway' => ['nullable','string'],
            'transaction_id' => ['nullable','string'],
            'amount' => ['required','numeric'],
            'fee' => ['nullable','numeric'],
            'paid_at' => ['nullable','date'],
            'reference_number' => ['nullable','string'],
            'document_proof' => ['nullable','string'],
            'status' => ['required','string'],
            'notes' => ['nullable','string'],
        ]);
        $data['created_by'] = $request->user()->id;
        return Payment::create($data);
    }
    public function show(Payment $payment) { return $payment; }
    public function update(Request $request, Payment $payment) {
        $data = $request->validate([
            'payment_method' => ['sometimes','required','string'],
            'payment_gateway' => ['nullable','string'],
            'transaction_id' => ['nullable','string'],
            'amount' => ['sometimes','required','numeric'],
            'fee' => ['nullable','numeric'],
            'paid_at' => ['nullable','date'],
            'reference_number' => ['nullable','string'],
            'document_proof' => ['nullable','string'],
            'status' => ['sometimes','required','string'],
            'notes' => ['nullable','string'],
        ]);
        $payment->update($data); return $payment;
    }
    public function destroy(Payment $payment) { $payment->delete(); return response()->noContent(); }
}