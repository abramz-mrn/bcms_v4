<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceNumberService;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::query()
            ->with(['customer','subscription','product'])
            ->orderBy('id','desc')
            ->paginate(20);
    }

    public function store(InvoiceNumberService $invoiceNo)
    {
        $data = request()->validate([
            'customer_id' => ['required','exists:customers,id'],
            'subscription_id' => ['required','exists:subscriptions,id'],
            'product_id' => ['required','exists:products,id'],
            'period_start' => ['required','date'],
            'period_end' => ['required','date'],
            'amount' => ['required','integer','min:0'],
            'tax_amount' => ['nullable','integer','min:0'],
            'discount_amount' => ['nullable','integer','min:0'],
            'total_amount' => ['required','integer','min:0'],
            'due_date' => ['required','date'],
            'status' => ['nullable','string','max:20'], // Unpaid|Paid
        ]);

        $data['invoice_no'] = $invoiceNo->nextInvoiceNo();
        $data['created_by'] = request()->user()->id;
        $data['status'] = $data['status'] ?? 'Unpaid';

        return Invoice::create($data)->load(['customer','subscription','product']);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['customer','subscription','product']);
    }

    public function update(Invoice $invoice)
    {
        $data = request()->validate([
            'period_start' => ['sometimes','date'],
            'period_end' => ['sometimes','date'],
            'amount' => ['sometimes','integer','min:0'],
            'tax_amount' => ['nullable','integer','min:0'],
            'discount_amount' => ['nullable','integer','min:0'],
            'total_amount' => ['sometimes','integer','min:0'],
            'due_date' => ['sometimes','date'],
            'status' => ['nullable','string','max:20'],
        ]);

        $invoice->update($data);
        return $invoice->load(['customer','subscription','product']);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();
        return response()->json(['ok' => true]);
    }
}