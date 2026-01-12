<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index() { return Invoice::query()->latest()->paginate(20); }
    public function store(Request $request) {
        $data = $request->validate([
            'invoice_no' => ['required','string','max:50','unique:invoices,invoice_no'],
            'customers_id' => ['required','integer','exists:customers,id'],
            'subscriptions_id' => ['required','integer','exists:subscriptions,id'],
            'products_id' => ['required','integer','exists:products,id'],
            'period_start' => ['required','date'],
            'period_end' => ['required','date'],
            'amount' => ['required','numeric'],
            'tax_amount' => ['required','numeric'],
            'discount_amount' => ['required','numeric'],
            'total_amount' => ['required','numeric'],
            'due_date' => ['required','date'],
            'status' => ['required','string'],
        ]);
        $data['created_by'] = $request->user()->id;
        return Invoice::create($data);
    }
    public function show(Invoice $invoice) { return $invoice; }
    public function update(Request $request, Invoice $invoice) {
        $data = $request->validate([
            'invoice_no' => ['sometimes','required','string','max:50','unique:invoices,invoice_no,'.$invoice->id],
            'customers_id' => ['sometimes','required','integer','exists:customers,id'],
            'subscriptions_id' => ['sometimes','required','integer','exists:subscriptions,id'],
            'products_id' => ['sometimes','required','integer','exists:products,id'],
            'period_start' => ['sometimes','required','date'],
            'period_end' => ['sometimes','required','date'],
            'amount' => ['sometimes','required','numeric'],
            'tax_amount' => ['sometimes','required','numeric'],
            'discount_amount' => ['sometimes','required','numeric'],
            'total_amount' => ['sometimes','required','numeric'],
            'due_date' => ['sometimes','required','date'],
            'status' => ['sometimes','required','string'],
        ]);
        $invoice->update($data); return $invoice;
    }
    public function destroy(Invoice $invoice) { $invoice->delete(); return response()->noContent(); }
}