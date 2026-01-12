<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $cust = Customer::query()->where('code','CUST-0001')->firstOrFail();
        $prod = Product::query()->where('code','BASIC-10')->firstOrFail();

        Ticket::query()->create([
            'ticket_number' => 'TIC-2026-0001',
            'customers_id' => $cust->id,
            'products_id' => $prod->id,
            'caller_name' => 'Budi Santoso',
            'phone' => '081300000001',
            'email' => 'budi@example.local',
            'category' => 'technical',
            'priority' => 'high',
            'subject' => 'Internet putus',
            'description' => 'Sejak pagi koneksi putus total.',
            'status' => 'open',
            'sla_due_date' => now()->addHours(8),
        ]);
    }
}