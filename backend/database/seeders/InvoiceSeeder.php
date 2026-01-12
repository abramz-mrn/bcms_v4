<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\InvoiceNumberService;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $svc = app(InvoiceNumberService::class);

        $s1 = Subscription::where('subscription_no','SUB-000001')->firstOrFail();
        $s2 = Subscription::where('subscription_no','SUB-000002')->firstOrFail();

        $periodStart = now()->startOfMonth()->toDateString();
        $periodEnd = now()->endOfMonth()->toDateString();
        $due = now()->startOfMonth()->addDays(20)->toDateString();

        $make = function(Subscription $s, string $status) use ($svc, $periodStart, $periodEnd, $due) {
            $amount = $s->product->price;
            $tax = (int) round($amount * ((float)$s->product->tax_rate / 100.0));
            $discount = 0;
            $total = $amount + $tax - $discount;

            return Invoice::create([
                'invoice_no' => $svc->nextInvoiceNo(),
                'customer_id' => $s->customer_id,
                'subscription_id' => $s->id,
                'product_id' => $s->product_id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'amount' => $amount,
                'tax_amount' => $tax,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'due_date' => $due,
                'status' => $status,
            ]);
        };

        // Ensure relations loaded
        $s1->load('product');
        $s2->load('product');

        // Create 2 invoices
        $make($s1, 'Unpaid');
        $make($s2, 'Paid');
    }
}