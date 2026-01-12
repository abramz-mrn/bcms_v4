<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->where('name','Fandi')->firstOrFail();
        $invoice = Invoice::query()->where('invoice_no','INV-2026-0001')->firstOrFail();

        Payment::query()->create([
            'invoices_id' => $invoice->id,
            'payment_method' => 'transfer',
            'payment_gateway' => null,
            'transaction_id' => null,
            'amount' => 150000,
            'fee' => 0,
            'paid_at' => null,
            'reference_number' => 'TRX-DUMMY-0001',
            'document_proof' => null,
            'status' => 'pending',
            'notes' => 'Dummy transfer waiting verification',
            'created_by' => $creator->id,
        ]);
    }
}