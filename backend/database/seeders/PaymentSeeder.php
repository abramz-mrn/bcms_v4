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
        $creator = User::where('email', 'meci@maroon-net.id')->first();

        $paidInvoice = Invoice::where('status','Paid')->first();
        if (!$paidInvoice) return;

        Payment::create([
            'invoice_id' => $paidInvoice->id,
            'payment_method' => 'cash',
            'payment_gateway' => null,
            'transaction_id' => null,
            'amount_paid' => $paidInvoice->total_amount,
            'paid_at' => now()->subDays(1),
            'ref_number' => 'CASH-001',
            'status' => 'verified',
            'notes' => 'Seed cash payment',
            'created_by' => $creator?->id,
        ]);
    }
}