<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::query()->where('name','Fandi')->firstOrFail();
        $sub = Subscription::query()->firstOrFail();
        $cust = Customer::query()->findOrFail($sub->customers_id);
        $prod = Product::query()->findOrFail($sub->products_id);

        Invoice::query()->create([
            'invoice_no' => 'INV-2026-0001',
            'customers_id' => $cust->id,
            'subscriptions_id' => $sub->id,
            'products_id' => $prod->id,
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'amount' => 150000,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 150000,
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'Unpaid',
            'created_by' => $creator->id,
        ]);
    }
}